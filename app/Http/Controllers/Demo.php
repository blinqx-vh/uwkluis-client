<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface;
use Ufo\Client\Connection\Config;
use Ufo\Client\Connection\ConnectToAccount;
use Ufo\Client\Connection\Scopes;
use Ufo\Client\Registration\Register;

final class Demo
{
    /** @var Config */
    private $ufoConfig;

    public function __construct(Scopes $scopes)
    {
        $this->ufoConfig = new Config('AwesomeApp',
            'http://client.ufo.local/connect/callback',
            3,
            '8oeoreeS02s8MEkh1HwnsN9VpSFgNP3z79tXE82a',
            $scopes->getScopes()
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
        dd($accessTokenResponse, __FILE__ . ':' . __LINE__);
    }


}
