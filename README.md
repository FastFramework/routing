# Routing - PHP Library

[![](https://img.shields.io/packagist/v/assouan/routing.svg)](https://packagist.org/packages/assouan/routing)
[![](https://img.shields.io/packagist/dt/assouan/routing.svg)](https://packagist.org/packages/assouan/routing)
[![](https://img.shields.io/packagist/l/assouan/routing.svg)](https://packagist.org/packages/assouan/routing)

Router class for PSR-7 http

## Installation

Install using composer:

```bash
$ composer require assouan/routing
```

## Usage

```php
$router = new Routing\Router();

$router->map($regex1, $callable1);
$router->map($regex2, $callable2);
$router->map($regex3, $callable3);

$response = $router->match($request);

echo $reponse;
```

## Example

```php
$router = new Routing\Router();

$router->map('/', 'sample_func');
$router->map('/special', 'SampleController::special')->setOption('method', 'post')->setOption('scheme', 'https');
$router->map('/hello/(?<name>\w+)', 'SampleController::hello');

$response = $router->match($request); // $request = Psr\Http\Message\ServerRequestInterface

echo $response;
```

```php
function sample_func()
{
    return 'Hello world!';
}
```

```php
class SampleController
{
    public function special()
    {
        return 'Page only in HTTPS POST';
    }

    public static function hello($request, $name)
    {
        // with function parameter
        $username = $name;
        // with request attribute
        $username = $request->getAttribute('name');

        return 'Hello '.$username;
    }
}
```
