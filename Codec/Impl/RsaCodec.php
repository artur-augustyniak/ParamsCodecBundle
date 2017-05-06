<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 Artur Augustyniak
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Aaugustyniak\ParamsCodecBundle\Codec\Impl;

use Aaugustyniak\ParamsCodecBundle\Codec\ParamCodec;
use Psr\Log\InvalidArgumentException;

/**
 * Class RsaCodec
 * @package Aaugustyniak\ParamsCodecBundle\Codec\Impl
 */
class RsaCodec implements ParamCodec
{
    
    /**
     * @var string
     */
    private $privKey;
    
    
    /**
     * @var string
     */
    private $pubKey;


    /**
     * RsaCodec constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        if (!$key || !is_string($key)) {
            throw new InvalidArgumentException(
                '$key must be not null string'
            );
        }
        
        // Create the keypair
        $res=openssl_pkey_new(array(
            "digest_alg" => "sha512",
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        // Get private key
        openssl_pkey_export($res, $this->privKey);


        // Get public key
        $keys=openssl_pkey_get_details($res);
        $this->pubKey=$keys["key"];

        
    }

    /**
     * @param $param
     * @param $key
     * @return string
     */
    public function encodeParam($param, $key = null)
    {
        if (!$param) {
            throw new InvalidArgumentException("en/decode prams cannot be null");
        }
        $crypted = "";
        openssl_public_encrypt($param, $crypted, $this->pubKey);
        return bin2hex($crypted);
    }

    /**
     * @param $param
     * @param $key
     * @return string
     */
    public function decodeParam($param, $key = null)
    {
        if (!$param) {
            throw new InvalidArgumentException("en/decode prams cannot be null");
        }
        $decrypted = "";    
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            openssl_private_decrypt(hex2bin($param), $decrypted, $this->privKey);
        } else {
            openssl_private_decrypt(pack("H*", $param), $decrypted, $this->privKey);
        }
        return $decrypted;
    }
    
}
