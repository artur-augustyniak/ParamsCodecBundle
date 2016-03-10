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


namespace Aaugustyniak\ParamsCodecBundle\Tests\Codec;

use PHPUnit_Framework_TestCase as TestCase;
use Aaugustyniak\ParamsCodecBundle\Codec\Impl\XorCodec;

/**
 * Class XorCodecTest
 * @author Artur Augustyniak <artur@aaugustyniak.pl>
 * @package Aaugustyniak\ParamsCodecBundle\Tests\Codec
 */
class XorCodecTest extends TestCase
{
    const TEST_KEY = "some secret key";
    const CUSTOM_TEST_KEY = "some other secret key";
    const TEST_SECRET_VAL = "some secret value";

    /**
     * @var XorCodec
     */
    private $codec;

    /**
     * @before
     */
    public function beforeEachTest()
    {
        $this->codec = new XorCodec(self::TEST_KEY);
    }

    /**
     * @test
     * @expectedException \PSR\Log\InvalidArgumentException
     */
    public function
    codec_constructor_expects_not_null()
    {
        new XorCodec(null);
    }

    /**
     * @test
     * @expectedException \PSR\Log\InvalidArgumentException
     */
    public function
    codec_constructor_expects_string()
    {
        new XorCodec(new \DateTime());
    }

    /**
     * @test
     */
    public function
    default_key_encryption_test()
    {
        $encrypted = $this->codec->encodeParam(self::TEST_SECRET_VAL);
        $this->assertNotEquals(self::TEST_SECRET_VAL, $encrypted);
        $decrypted = $this->codec->decodeParam($encrypted);

        $this->assertEquals(self::TEST_SECRET_VAL, $decrypted);
    }

    /**
     * @test
     */
    public function
    custom_key_encryption_test()
    {
        $encrypted = $this->codec->encodeParam(
            self::TEST_SECRET_VAL,
            self::CUSTOM_TEST_KEY
        );
        $this->assertNotEquals(self::TEST_SECRET_VAL, $encrypted);
        $decrypted = $this->codec->decodeParam(
            $encrypted,
            self::CUSTOM_TEST_KEY
        );
        $this->assertEquals(self::TEST_SECRET_VAL, $decrypted);
    }

    /**
     * @test
     */
    public function
    default_custom_mix_key_encryption_failure_test()
    {
        $encrypted = $this->codec->encodeParam(self::TEST_SECRET_VAL);
        $decrypted = $this->codec->decodeParam(
            $encrypted,
            self::CUSTOM_TEST_KEY
        );
        $this->assertNotEquals(self::TEST_SECRET_VAL, $decrypted);
    }

    /**
     * @test
     */
    public function
    custom_default_mix_key_encryption_failure_test()
    {
        $encrypted = $this->codec->encodeParam(
            self::TEST_SECRET_VAL,
            self::CUSTOM_TEST_KEY
        );
        $decrypted = $this->codec->decodeParam(
            $encrypted
        );
        $this->assertNotEquals(self::TEST_SECRET_VAL, $decrypted);
    }

    /**
     * @test
     */
    public function
    codec_with_custom_key_should_change_param_different_then_default_key()
    {
        $expected = $this->codec->encodeParam(
            self::TEST_SECRET_VAL,
            self::CUSTOM_TEST_KEY
        );
        $actual = $this->codec->encodeParam(
            self::TEST_SECRET_VAL
        );
        $this->assertNotEquals($expected, $actual);
    }

    /**
     * @test
     * @expectedException \PSR\Log\InvalidArgumentException
     */
    public function
    null_param_encode()
    {
        $this->codec->encodeParam(null);
    }

    /**
     * @test
     * @expectedException \PSR\Log\InvalidArgumentException
     */
    public function
    null_param_decode()
    {
        $this->codec->decodeParam(null);
    }
}
