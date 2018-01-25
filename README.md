# UwKluis Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-coverage]][link-coverage]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


This package can assist in connecting to the [UwKluis API](https://api.uwkluis.nl). An [OpenAPI](https://www.openapis.org/) 
compatible schema and [Swagger](https://swagger.io/) -generated documentation about the individual endpoints can be 
found at the root of this domain. 
## Structure


```
src/
src/Consumer
src/Exception
src/Organization
src/Traits
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
A user can then have an invitation sent to the consumer based on their phone number and e-mail address. Your client now needs the previously stored access token.
The resulting response object will contain the organization consumer id and the UFO consumer id (a UUID). Store these
for future requests.
```
$organizationConsumerId = 'JOHNSON_AMSTERDAM_001';
$consumerConnect = new \Ufo\Client\Consumer\Connect($config, $guzzleClient);
$connection = $consumerConnect->inviteConsumer('johnson@example.org', '0612345678');
$uwKluisConsumerId = $connection->getUwKluisConsumerId();
```
If the user's organization already has a connection, the request will fail, throwing a 
`\Ufo\Client\Exception\ConsumerConnectionConflict`, which contains the conflicting connection. This can be extracted via
`\Ufo\Client\Exception\ConsumerConnectionConflict::getConflictingConnection()`.
 Information about an existing connection, such as one returned by the aforementioned exception, can be retrieved using:
```
 /** @var $consumerConnect \Ufo\Client\Consumer\Connect */
 /** @var $token \Lcobucci\JWT\Token */
 /** @var $uwKluisConsumerId \Ramsey\Uuid\UuidInterface */
 /** @var $connection \Ufo\Client\Consumer\Connect */
$connection = $consumerConnect->getConnectionStatus($token, $uwKluisConsumerId);
``` 
 If an invitation has been sent to a consumer, but they are unable to accept it because either the phonenumber or the
 e-mail address are incorrect, or they are unable to find the invitation, one can send a new invitation using the existing
 connection ID:
 ```
/** @var $consumerConnect \Ufo\Client\Consumer\Connect */
/** @var $token \Lcobucci\JWT\Token */
/** @var $uwKluisConsumerId \Ramsey\Uuid\UuidInterface */
$consumerConnect->updateAndReinviteConsumer($token, $uwKluisConsumerId, 'newEmail@example.org', '0612345678');
 ``` 
First request the current consumer dossier, using the organization's access token, the organization consumer id and
the desired consumer dossier json schema version:
```
$dossier = new \Ufo\Client\Consumer\Dossier($config, $guzzleClient);
$response = $dossier->getData(
    $accessToken,
    $uwKluisConsumerId,
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
    $uwKluisConsumerId,
    $mergedData
    1
);
```

## Webhooks
The UwKluis API provides several webhook options, so your client can be notified if a consumer connection changes (for 
instance when a consumer establishes a connection, or changes the granted scopes) or when a consumer
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

If you discover any security related issues, please email info@hypotheekbond.nl instead of using the issue tracker.

## Credits

- [THE UwKluis TEAM][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ufo/client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-coverage]: https://scrutinizer-ci.com/gp/ufo-client/badges/coverage.png?b=master&s=a17ac8f96ccf1ce175eb73203702d8398346d7a6
[ico-code-quality]: https://scrutinizer-ci.com/gp/ufo-client/badges/quality-score.png?b=master&s=6a0bcea240c60e70290bf3857c2966e9c38b29c8
[ico-downloads]: https://img.shields.io/packagist/dt/ufo/client.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/ufo/client
[link-coverage]: https://scrutinizer-ci.com/g/ufo/client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ufo/client
[link-downloads]: https://packagist.org/packages/ufo/client
[link-author]: https://github.com/REDACTED