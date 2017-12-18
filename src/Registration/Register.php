<?php
declare(strict_types = 1);

namespace Ufo\Client\Registration;

use GuzzleHttp\Client as GuzzleClient;
use Ufo\Client\Connection\Config;

final class Register
{
    /** @var GuzzleClient */
    private $client;
    /** @var Config */
    private $clientConfig;

    /**
     * Register constructor.
     *
     * @param Config       $clientConfig
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->clientConfig = new Config(
            'AwesomeApp',
            'http://client.ufo.local/connect/callback'
        );
        $this->client = $client;
    }

    public function register()
    {
        $data = [
            'name' => $this->clientConfig->getClientName(),
            'redirect' => $this->clientConfig->getCallbackUri(),
        ];

        $response = $this->client->post('organization.ufo.local/oauth/clients', $data)->getBody()->getContents();
        dd($response);
    }
}
