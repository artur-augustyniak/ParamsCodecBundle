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
namespace Aaugustyniak\ParamsCodecBundle\Annotations\Driver;

use Aaugustyniak\ParamsCodecBundle\Annotations\DecryptParams;
use Aaugustyniak\ParamsCodecBundle\Codec\ParamCodec;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class DecryptParamsDriver
 * @package Aaugustyniak\ParamsCodecBundle\Annotations\Driver
 */
class DecryptParamsDriver
{

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ParamCodec
     */
    private $codec;


    /**
     * Constructor.
     *
     * @param Reader $reader
     * @param ParamCodec $codec
     */
    public function __construct(Reader $reader, ParamCodec $codec)
    {
        $this->reader = $reader;
        $this->codec = $codec;
    }

    /**
     * This event will fire during any controller call
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }
        if ('GET' === $event->getRequest()->getMethod()) {
            $object = new \ReflectionObject($controller[0]);
            $method = $object->getMethod($controller[1]);
            foreach ($this->reader->getMethodAnnotations($method) as $annotation) {

                if ($annotation instanceof DecryptParams) {
                    $route_params = $event->getRequest()->attributes->all()['_route_params'];
                    foreach ($route_params as $k => $v) {
                        $event->getRequest()->attributes->set($k, $this->codec->decodeParam($v));
                    }
                }
            }
        }
    }
}