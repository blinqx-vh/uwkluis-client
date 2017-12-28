<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use Lcobucci\JWT\Token;
use Ufo\Client\Organization\Config;

final class Connect
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Connection constructor.
     *
     * @param GuzzleClient $guzzleClient
     * @param Config       $config
     */
    public function __construct(
        GuzzleClient $guzzleClient,
        Config $config
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
        $query = http_build_query([
            'organization_consumer_id' => $organizationConsumerId,
        ]);
        $httpResponse = $this->guzzleClient->get(
            $this->config->getApiHost() . 'api/consumer-connection?' . $query,
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . (string) $accessToken,
                ],
            ]);
        $response = json_decode($httpResponse->getBody()->getContents(), true);

        return new Connection(
            $response['organization_consumer_id'],
            $response['ufo_consumer_id'],
            $response['connection_code'],
            explode(' ', $response['granted_scopes'])
        );
    }
}
