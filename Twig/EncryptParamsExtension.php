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
namespace Aaugustyniak\ParamsCodecBundle\Twig;


use Aaugustyniak\ParamsCodecBundle\Codec\ParamCodec;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use \Twig_Extension as Twig;
use \Twig_SimpleFunction as TwigSimpleFunction;

/**
 * Class EncryptParamsExtension
 * @package Aaugustyniak\ParamsCodecBundle\Twig
 */
class EncryptParamsExtension extends Twig
{

    /**
     * @var ParamCodec
     */
    private $codec;

    /**
     * @var Router
     */
    private $router;


    /**
     * Constructor.
     *
     * @param ParamCodec $codec
     * @param Router $router
     */
    public function __construct(ParamCodec $codec, Router $router)
    {
        $this->codec = $codec;
        $this->router = $router;
    }


    public function getFunctions()
    {
        return array(
            new TwigSimpleFunction('encrypted_path', array($this, 'encryptedPath')),
            new TwigSimpleFunction('encrypted_url', array($this, 'encryptedUrl')),
        );
    }

    public function encryptedPath()
    {
        list($route, $params) = $this->prepareParams(func_get_args());
        return $this->router->generate($route, $params);
    }

    public function encryptedUrl()
    {
        list($route, $params) = $this->prepareParams(func_get_args());
        return $this->router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'encrypt_params_extension';
    }

    /**
     * @param $args
     * @return array
     */
    private function prepareParams($args)
    {
        $route = $args[0];
        $params = array();
        if (isset($args[1]) && is_array($args[1])) {
            $params = $args[1];

        }
        foreach ($params as $name => $v) {
            $params[$name] = $this->codec->encodeParam($v);
        }
        return array($route, $params);
    }
}