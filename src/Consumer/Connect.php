<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use Ufo\Client\Connection\AccessTokenResponse;
use Ufo\Client\Connection\Config;

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

    public function getConnection(
        AccessTokenResponse $accessToken,
        string $organizationConsumerId
    ) {
        $query = http_build_query([
            'organization_consumer_id' => $organizationConsumerId,
        ]);
        $httpResponse = $this->guzzleClient->get(
            $this->config->getApiHost() . 'api/consumer-connection?' . $query,
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken->getAccessToken(),
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
