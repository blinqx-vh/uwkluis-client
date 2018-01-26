# UFO Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabsInsight][ico-sensiolabs]][link-sensiolabs]


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

First setup the route and controller to handle the OAuth2 callback. Store the (data from the) token response in some 
fashion.
``` php
$config = new \Ufo\Client\Organization\Config(
    'registered-client-name',
    'registered-callback-url',
    1,
    'registered-client-secret',
    ['read-person-data', 'read-write-mortgages-data']
);
$guzzleClient = new GuzzleHttp\Client();
$organizationConnect = new Ufo\Client\Organization\Connect($config, $guzzleClient);
$accessTokenResponse = $organizationConnect->processResponse($psr7request);
$accessToken = $accessTokenResponse->getAccessToken();
$refreshToken = $accessTokenResponse->getRefreshToken();
$expiration = $accessTokenResponse->getExpiration();
```
Send organization users to the Oauth2 url to establish a connection. A request will be sent to the callback 
route in response.
```
/** @var $connect Ufo\Client\Organization\Connect */
echo '<a href="' . $connect->getAuthorizeUrl() . '">Connect</a>';
```
A user can then request a new consumer connection based on their organization's relation / reference number
(referred to as organization consumer id) for that consumer. Your client now needs the previously stored access token.
The resulting response object will contain the organization consumer id and the UFO consumer id (a UUID). Store these
for future requests.
```
$organizationConsumerId = 'JOHNSON_AMSTERDAM_001';
$consumerConnect = new \Ufo\Client\Consumer\Connect($config, $guzzleClient);
$connection = $consumerConnect->getConnection($accessToken, $organizationConsumerId);
$ufoConsumerId = $connection->getUfoConsumerId();
```
If the user's organization already has a connection, it will also contain the granted scopes. If it  hasn't established 
a connection with that consumer before, it will instead contain a connection code. Both the organization consumer id
and the connection code need to be communicated with the consumer, who can then use those to either create a new UFO 
account and connect it to that organization, or connect an already existing account. From that point onwards, your
client software can retrieve (and possibly edit) consumer dossier data using the UFO consumer id.
 
First request the current consumer dossier, using the organization's access token, the organization consumer id and
the desired consumer dossier json schema version:
```
$dossier = new \Ufo\Client\Consumer\Dossier($config, $guzzleClient);
$response = $dossier->getData(
    $accessToken,
    $ufoConsumerId,
    1
);
$data = $response['data'];
```
If you wish to update the consumer dossier data, first merge the retrieved data with your own, and then send that back
using the updateData() method.
```
/** @var array $mergedData */
$dossier->updateData(
    $accessToken,
    $ufoConsumerId,
    $mergedData
    1
);
```

## Webhooks
The UFO API provides several webhook options, so your client can be notified if a consumer connection changes (for 
instance when a consumer establishes a connection using their code, or changes the granted scopes) or when a consumer
dossier changes (either by the consumer or by another client application or organization). Registering webhooks can
be done after an organization user has established a connection using Oauth2. The 
[ufo/webhook-client](https://packagist.org/packages/ufo/webhook-client) package can help implementing webhooks in your 
client application.

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
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/4ab172be-9cc4-464c-aa2e-93566244b1ac.svg

[link-packagist]: https://packagist.org/packages/ufo/client
[link-travis]: https://travis-ci.org/ufo/client
[link-scrutinizer]: https://scrutinizer-ci.com/g/ufo/client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ufo/client
[link-downloads]: https://packagist.org/packages/ufo/client
[link-author]: https://github.com/REDACTED
[link-contributors]: ../../contributors
[link-sensiolabs]: https://insight.sensiolabs.com/projects/4ab172be-9cc4-464c-aa2e-93566244b1ac
