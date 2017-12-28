<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

use GuzzleHttp\Client as GuzzleClient;

final class Scopes
{
    /** @var GuzzleClient */
    private $client;
    /** @var Config */
    private $config;

    /**
     * Scopes constructor.
     *
     * @param GuzzleClient $client
     * @param Config       $config
     */
    public function __construct(GuzzleClient $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * returns an associative array with available scopes and their Dutch translations
     *
     * @return array
     */
    public function getScopes(): array
    {
        $response = $this->client->get($this->config->getApiHost() . '/api/scopes')->getBody()->getContents();
        return json_decode($response, true);
    }
}
