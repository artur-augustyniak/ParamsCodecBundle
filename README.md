# ParamsCodecBundle
===================
[![Build Status](https://travis-ci.org/artur-augustyniak/ParamsCodecBundle.svg?branch=master)]
(https://travis-ci.org/artur-augustyniak/ParamsCodecBundle)

Provides a simple Symfony 2/3 Bundle to AES encrypt routing parameters.

## Installing via Composer

```json
{
    "require": {
        "aaugustyniak/params-codec-bundle": "dev-master"
    }
}
```

## Using and Setting Up

### composer
```
composer aaugustyniak/params-codec-bundle
```

### AppKernel.php
```php
$bundles = [
    ...,
    new Aaugustyniak\ParamsCodecBundle\ParamsCodecBundle()
];
```

## Basic usage
This bundle registers ```param_codec``` service and 
twig extension providing two functions in relation with standard 
Twig url creation functions.

In default implementation ParamCodec interface is backed by AesCodec. 
ParamCodec uses secret passphrase from parameters.yml.

### DefaultController.php
```php
use Aaugustyniak\ParamsCodecBundle\Annotations\DecryptParams;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /**
         * Value to be encrypted in resulting url/path
         */
        $rawValue = "Some secret internal value";
        return $this->render('default/index.html.twig', [
            'raw_value' => $rawValue,
        ]);
    }


    /**
     * @DecryptParams()
     *
     * @Route("/display/{param}", name="display")
     * @param $param
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayAction($param)
    {
        /**
         * Using @DecryptParams() annotation $param is auto decrypted
         */
        return $this->render('default/index.html.twig', [
            'decrypted_value' => $param
        ]);

    }
    
}
```

### index.html.twig
```twig
{% extends 'base.html.twig' %}

{% block body %}
    <div id="wrapper" xmlns="http://www.w3.org/1999/html">
        <div id="container">
            <div>
                {% if decrypted_value is defined %}
                    <div style="border: 1px dotted red;background: green;padding: 16px;">
                        Decrypted param: <code>{{ decrypted_value }}</code>
                        <a href="{{ path('homepage') }}">
                            BACK
                        </a>
                    </div>
                {% else %}
                    <p>
                        {% verbatim %}
                            {{ raw_value }}
                        {% endverbatim %}
                        <code>{{ raw_value }}</code>
                    </p>
                    <!--
                    Functions:
                        encrypted_path
                        encrypted_url
                    are equivalent of basic twig path and url
                    -->
                    <div style="border: 1px dotted green;padding: 16px; margin-top: 12px;">
                        <a href="{{ encrypted_path('display', {'param': raw_value}) }}">
                            {% verbatim %}
                           {{ encrypted_path('display', {'param': raw_value}) }}
                        {% endverbatim %}
                        </a>
                        <a href="{{ encrypted_url('display', {'param': raw_value}) }}">
                            {% verbatim %}
                           {{ encrypted_url('display', {'param': raw_value}) }}
                        {% endverbatim %}
                        </a>

                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock %}
```