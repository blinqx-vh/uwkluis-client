<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Ufo\Client\Connection\AccessTokenResponse;
use Ufo\Client\Connection\Config;
use Ufo\Client\Connection\ConnectToAccount;
use Ufo\Client\Connection\Scopes;

final class Demo
{
    /** @var Config */
    private $ufoConfig;

    public function __construct(Scopes $scopes)
    {
        $this->ufoConfig = new Config('democlient',
            'http://client.ufo.local/connect/callback',
            1,
            '8oeoreeS02s8MEkh1HwnsN9VpSFgNP3z79tXE82a',
            array_keys($scopes->getScopes())
        );
    }

    public function getScopes(Scopes $scopes)
    {
        $scopes->getScopes();
    }

    public function connect()
    {
        $view =
            '<a href="' . (new ConnectToAccount($this->ufoConfig, new Client()))->getRedirectUrl() . '">Connect</a>';
        echo $view;
        exit;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function connectionCallback(ServerRequestInterface $request)
    {
        $accessTokenResponse = (new ConnectToAccount($this->ufoConfig, new Client()))->processResponse($request);
        file_put_contents(storage_path('app/oauth/accesstokenResponse.serialized'), serialize($accessTokenResponse));

        return new RedirectResponse('/');
    }

    public function getAddress(Request $request)
    {
        $serialized = file_get_contents(storage_path('app/oauth/accesstokenResponse.serialized'));
        /** @var AccessTokenResponse $unserialized */
        $unserialized = unserialize($serialized, [AccessTokenResponse::class]);
        $response = (new Client())->get('http://organization.ufo.local/api/address?consumer_id=' . $request->query('consumer_id'), [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $unserialized->getAccessToken(),
            ],
        ])->getBody()->getContents();
        dd(json_decode($response));
    }


    public function getConsumerConnection()
    {
        $serialized = file_get_contents(storage_path('app/oauth/accesstokenResponse.serialized'));
        /** @var AccessTokenResponse $unserialized */
        $unserialized = unserialize($serialized, [AccessTokenResponse::class]);

        $query = http_build_query([
            'organization-consumer-identifier' => 'klantje1'
        ]);
        $response = json_decode((new Client())->get('http://organization.ufo.local/api/get-consumer-connection?' . $query, [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $unserialized->getAccessToken(),
            ],
        ])->getBody()->getContents(), true);
        echo '<a href="/demo/get-address?consumer_id=' . $response['ufo-consumer-identifier'] . '" target="_blank">Adres</a>';
        dd($response);
    }
}
