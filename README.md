# Laravel with limits

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Laravel with limits is a Laravel package that handles API rate limits, down to the method level. The package will read rate limit headers and keep track of requests to prevent exceeding rate limits. GuzzleHttp is used to send requests and receive responses.

## Install

Via Composer

``` bash
$ composer require jonathanstaniforth/laravel-with-limits
```

## Usage

``` php
$request = new LaravelWithLimits\Request();

$response = $request->method('GET')
    ->path('static-data/v3/champions')
    ->withParameters(['locale' => 'en_GB', 'tags' => 'all'])
    ->withLimit(function ($rate_limit) {
        $rate_limit->api('riot')
            ->endpoint('static-data/v3/champions')
            ->header('X-Method-Rate-Limit');
    })->send();
```

## Security

If you discover any security related issues, please email jonathanstaniforth@gmail.com instead of using the issue tracker.

## Credits

- [Jonathan Staniforth][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jonathanstaniforth/laravel-with-limits.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jonathanstaniforth/laravel-with-limits.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jonathanstaniforth/laravel-with-limits
[link-downloads]: https://packagist.org/packages/jonathanstaniforth/laravel-with-limits
[link-author]: https://github.com/jonathanstaniforth
[link-contributors]: ../../contributors
