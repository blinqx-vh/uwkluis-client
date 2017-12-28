<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

use GuzzleHttp\Client as GuzzleClient;

final class Scopes
{
    /** @var GuzzleClient */
    private $client;

    /**
     * Scopes constructor.
     *
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    /**
     * returns an associative array with available scopes and their Dutch translations
     *
     * @return array
     */
    public function getScopes(): array
    {
        $response = $this->client->get('organization.ufo.local/api/scopes')->getBody()->getContents();
        return json_decode($response, true);
    }
}
