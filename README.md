# Profiler

Lightweight app timer and profiling middleware for [Phico](https://github.com/phico-php/phico)

## Installation

Using composer

```sh
composer require phico/profiler
```

## Usage

Add the middleware to your application

```php
// /app/middleware.php

$app->use[
    ...
    new \Phico\Profiler\ProfilerMiddleware,
    ...
];

```

Start a timer

```php
$timer->start('account-action', 'The account action duration');
```

Check the response headers for the details

## Issues

Profiler is considered complete, however if you discover any bugs or issues in it's behaviour or performance please create an issue, and if you are able a pull request with a fix.

## License

[BSD-3-Clause](https://choosealicense.com/licenses/bsd-3-clause/)
