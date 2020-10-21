<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use GuzzleHttp\ClientInterface;

/**
 * Class Scopes
 */
final class Scopes
{
    /** @var ClientInterface */
    private $client;
    /** @var Config */
    private $config;

    /**
     * Scopes constructor.
     *
     * @param Config          $config
     * @param ClientInterface $client
     */
    public function __construct(
        Config $config,
        ClientInterface $client
    ) {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * returns an associative array with available scopes and their Dutch translations
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getScopes(): array
    {
        $response = $this->client->request('get', $this->config->getApiHost() . '/scopes')->getBody()->getContents();

        return json_decode($response, true);
    }
}
