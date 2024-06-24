<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use UwKluis\Client\Client\UwkluisClientInterface;

/**
 * Class Scopes
 */
final class Scopes
{
    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
    }

    /**
     * returns an associative array with available scopes and their Dutch translations
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getScopes(): array
    {
        $response = $this->uwkluisClient->request('get', $this->config->getApiHost() . '/scopes')->getBody()->getContents();

        return json_decode($response, true);
    }
}
