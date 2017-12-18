<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface;
use Ufo\Client\Connection\ConnectToAccount;
use Ufo\Client\Connection\Scopes;
use Ufo\Client\Registration\Register;

final class Demo
{
    public function getScopes(Scopes $scopes)
    {
        $scopes->getScopes();
    }

    public function connect(Scopes $scopes, Register $register)
    {
//        $register->register('AwesomeApp', 'foo.bar');
        $view = '<a href="'
            . (new ConnectToAccount([], new Client()))->getRedirectUrl(3, 'http://client.ufo.local/connect/callback'
            , array_keys($scopes->getScopes())) . '">Connect</a>';
        echo $view; exit;
    }

    /**
     * @param ServerRequestInterface $request
     */
    public function connectionCallback(ServerRequestInterface $request)
    {
        $code = (new ConnectToAccount([], new Client()))->processResponse($request);
        // def50200d9d55569d605393f323005e03befd24541cf8455ca51db017e3e95eac75a75b18a592d5c5c519a38f25c84c8b504ef875957c41e6b4b6d8a16d02050e954b3cac7f333ea529b95caf3c84d7f39b3b5a943e07a1a3f3e598e87e6b6b44f5dbdc1b1292dea98f2f4ba998c44680868f77d4544a0363733111a1f9828973ae0db86da98d46596a8024cd8f48060ed21982c82fea4f7f27d6e8913ea047762926b305fb2c802d64a5dfd8f6b7d9cc2a23e9ac7f523f3a8828fedfefa277583dc7bd7c8ceee56096474180a1ac83da7d98d4369e9a3153d2539f0486e61b91965c9e601ab249d562ecaca9a7610357b5fbc22a186221976be11ce992fdbc95c280e16f677bc253a2f3da0aee8a4cd2c62200cda6f9ebb3613ae253aba8cad564018e12de8b87ae6a396ac384c120358ea88d05cf7e3160f4991d2ff6e71fb6b4335a50e62a21b6b98c69a73ef940c61ff15a8ca88c5434c05cabf5837b0afa4fc8b44e7667f7fdcb690fd236f44899300862c3cb8fb53407fd5eaf810fb81c08423e8b13cbb7cf1175e11e3e8cdc554c6
    }


}
