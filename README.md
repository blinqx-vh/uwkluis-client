# UFO Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package can assist in connecting to the UFO API.
## Structure


```
src/
src/Client
src/Consumer
src/Exception
src/Organization
tests/
```


## Install

Via Composer

``` bash
$ composer require ufo/client
```

## Usage

``` php
$skeleton = new Ufo\Client();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email REDACTED instead of using the issue tracker.

## Credits

- [THE UFO TEAM][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ufo/client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ufo/client/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/ufo/client.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/ufo/client.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ufo/client.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/ufo/client
[link-travis]: https://travis-ci.org/ufo/client
[link-scrutinizer]: https://scrutinizer-ci.com/g/ufo/client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ufo/client
[link-downloads]: https://packagist.org/packages/ufo/client
[link-author]: https://github.com/REDACTED
[link-contributors]: ../../contributors
