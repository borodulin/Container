# Simple Fast Autowiring Container 
[![Build Status](https://travis-ci.org/borodulin/FastContainer.svg?branch=master)](https://travis-ci.org/borodulin/FastContainer)
[![Coverage Status](https://coveralls.io/repos/github/borodulin/FastContainer/badge.svg?branch=master)](https://coveralls.io/github/borodulin/FastContainer?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/borodulin/FastContainer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/borodulin/FastContainer/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/borodulin/FastContainer/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## Features

- [PSR-11](http://www.php-fig.org/psr/psr-11/) full featured implementation.
- Allows autowire via file finder and/or configuration file.
- Allows delegate lookup and resolve dependencies from other containers.
- Allows scalar types injection via parameters bag.
- Detects circular references.
- Supports aliasing.
- Supports closures.
- Supports [PSR-16](http://www.php-fig.org/psr/psr-16/) cache.

## Using container

Container can be initialized with file finder. 

```php
// autowire all classes in Samples directory
$fileFinder = (new \Borodulin\Container\Autowire\FileFinder())
    ->addPath(__DIR__.'/../Samples');
// build container
$container = (new \Borodulin\Container\ContainerBuilder())
    ->setFileFinder($fileFinder)
    ->build();
```

Container can be initialized via configuration file.

`./config/definitions.php`:
```php
<?php
return [
    'test.bar' => Bar::class,
    'test.foo' => function (Bar $bar) {
        return new Foo($bar);
    },
];
```

```php
// build container
$container = (new \Borodulin\Container\ContainerBuilder())
    ->setConfig(require(__DIR__ . '/config/definitions.php'))
    ->build();
```

After the container is built, objects can be obtained via `get()`:

```php
$object = $container->get('test.foo');
```
[But it is not recommended](https://www.php-fig.org/psr/psr-11/meta/#4-recommended-usage-container-psr-and-the-service-locator).

`“users SHOULD NOT pass a container into an object, so the object can retrieve its own dependencies. Users doing so are using the container as a Service Locator. Service Locator usage is generally discouraged.”`

## Using aliases

## Using closures

## Using parameters bag

## Delegate lookup

## Using cache
