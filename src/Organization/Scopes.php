<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class Scopes
 */
final class Scopes
{
    /** @var GuzzleClient */
    private $client;
    /** @var Config */
    private $config;

    /**
     * Scopes constructor.
     *
     * @param Config       $config
     * @param GuzzleClient $client
     */
    public function __construct(
        Config $config,
        GuzzleClient $client
    ) {
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
        $response = $this->client->get($this->config->getApiHost() . '/scopes')->getBody()->getContents();

        return json_decode($response, true);
    }
}
