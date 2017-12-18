<?php
declare(strict_types = 1);

namespace Ufo\Client\Registration;

use GuzzleHttp\Client as GuzzleClient;

final class Register
{
    /** @var GuzzleClient */
    private $client;

    /**
     * Register constructor.
     *
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function register(string $name, string $callbackUri = '')
    {
        $data = [
            'name' => $name,
            'redirect' => $callbackUri,
        ];

        $response = $this->client->post('organization.ufo.local/oauth/clients', $data)->getBody()->getContents();
        dd($response);
    }
}
