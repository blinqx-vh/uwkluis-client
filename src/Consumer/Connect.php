<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Organization\Config;

/**
 * Class Connect
 */
final class Connect
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Connection constructor.
     *
     * @param Config       $config
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(
        Config $config,
        GuzzleClient $guzzleClient
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    /**
     * @param Token  $accessToken
     * @param string $organizationConsumerId
     *
     * @return Connection
     */
    public function getConnection(
        Token $accessToken,
        string $organizationConsumerId
    ): Connection {
        $query = http_build_query(
            [
                'organization_consumer_id' => $organizationConsumerId,
            ]
        );
        $httpResponse = $this->guzzleClient->get(
            $this->config->getApiHost() . '/consumer-connection?' . $query,
            [
                RequestOptions::HEADERS => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . (string) $accessToken,
                ],
            ]
        );
        $response = json_decode($httpResponse->getBody()->getContents(), true);

        return new Connection(
            $response['organization_consumer_id'],
            $response['ufo_consumer_id'],
            $response['connection_code_1'],
            $response['connection_code_2'],
            explode(' ', $response['granted_scopes'])
        );
    }
}
