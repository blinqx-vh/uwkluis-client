<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
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

    public function getDossier(Request $request)
    {
        $serialized = file_get_contents(storage_path('app/oauth/accesstokenResponse.serialized'));
        /** @var AccessTokenResponse $unserialized */
        $unserialized = unserialize($serialized, [AccessTokenResponse::class]);

        $query = [
            'consumer_id' => $request->query('consumer_id'),
            'version' => 1
        ];
        $queryString = http_build_query($query);
        $response = (new Client())->get('http://organization.ufo.local/api/dossier?' . $queryString,
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $unserialized->getAccessToken(),
                ],
            ])->getBody()->getContents();

        return new JsonResponse(json_decode($response, true));
    }


    public function postDossier(Request $request)
    {
        $dossierResponse = $this->getDossier($request);
        $dossierData = $dossierResponse->getData(true);
        $houseNumber = $dossierData['data']['addresses'][0]['housenumber'];
        $houseNumber++;
        $dossierData['data']['addresses'][0]['housenumber'] = $houseNumber;
        $dossierData = json_encode($dossierData);
        $serialized = file_get_contents(storage_path('app/oauth/accesstokenResponse.serialized'));
        /** @var AccessTokenResponse $unserialized */
        $unserialized = unserialize($serialized, [AccessTokenResponse::class]);

        $query = [
            'consumer_id' => $request->query('consumer_id'),
            'version' => 1
        ];
        $queryString = http_build_query($query);
        $response = (new Client())->post('http://organization.ufo.local/api/dossier?' . $queryString,
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $unserialized->getAccessToken(),
                ],
                'form_params' => [
                    'dossier' => $dossierData
                ]
            ])->getBody()->getContents();

        return new JsonResponse(json_decode($response, true));

    }


    public function getConsumerConnection()
    {
        $serialized = file_get_contents(storage_path('app/oauth/accesstokenResponse.serialized'));
        /** @var AccessTokenResponse $unserialized */
        $unserialized = unserialize($serialized, [AccessTokenResponse::class]);
        $queryString = http_build_query([
            'organization_consumer_id' => 'klantje1',
        ]);
        $httpResponse = (new Client())->get('http://organization.ufo.local/api/consumer-connection?' . $queryString, [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $unserialized->getAccessToken(),
            ],
        ]);
        $response = json_decode($httpResponse->getBody()->getContents(), true);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
        echo '<p><a href="/demo/dossier?consumer_id=' . ($response['ufo_consumer_id'] ?? '') . '" target="_blank">Haal dossier op</a></p>';
        echo '<p><a href="/demo/postdossier?consumer_id=' . ($response['ufo_consumer_id'] ?? '') . '" target="_blank">Wijzig dossier (huisnummer++)</a></p>';
    }
}
