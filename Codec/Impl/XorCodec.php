<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Artur Augustyniak
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
 * Class XorCodec
 * @package Aaugustyniak\ParamsCodecBundle\Codec\Impl
 */
class XorCodec implements ParamCodec
{

    /**
     * @var string
     */
    private $key;


    /**
     * XorCodec constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        if (!$key || !is_string($key)) {
            throw new InvalidArgumentException(
                '$key must be not null string'
            );
        }
        $this->key = sha1($key);
    }

    /**
     * @param $param
     * @param $key
     * @return string
     */
    public function encodeParam($param, $key = null)
    {
        $key = null === $key ? $this->key : sha1($key);
        return bin2hex($this->xorString($param, $key));
    }

    /**
     * @param $param
     * @param $key
     * @return string
     */
    public function decodeParam($param, $key = null)
    {
        $key = null === $key ? $this->key : sha1($key);
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return $this->xorString(hex2bin($param), $key);
        } else {
            return $this->xorString(pack("H*", $param), $key);
        }
    }

    /**
     * @param $text
     * @param $key
     * @return string
     * @throws InvalidArgumentException
     */
    private function xorString($text, $key)
    {
        if (!($text && $key)) {
            throw new InvalidArgumentException("en/decode prams cannot be null");
        }
        $outText = '';
        for ($i = 0; $i < strlen($text);) {
            for ($j = 0; ($j < strlen($key) && $i < strlen($text)); $j++, $i++) {
                $outText .= $text{$i} ^ $key{$j};
            }
        }
        return $outText;
    }

}
