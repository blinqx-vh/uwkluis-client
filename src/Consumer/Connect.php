<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ramsey\Uuid\UuidInterface;
use Ufo\Client\Exception\ConsumerRequestException;
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

        try {
            $httpResponse = $this->guzzleClient->get(
                $this->config->getApiHost() . '/consumer-connection?' . $query,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$accessToken,
                    ],
                ]
            );
        } catch (\Exception $e) {
            throw new ConsumerRequestException('Consumer connection failed');
        }

        $response = json_decode($httpResponse->getBody()->getContents(), true);

        return new Connection(
            $response['organization_consumer_id'],
            $response['ufo_consumer_id'],
            $response['connection_code_1'],
            $response['connection_code_2'],
            explode(' ', $response['granted_scopes'])
        );
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return string
     */
    public function getOrganizationConsumerDossierUrl(UuidInterface $uuid): string
    {
        return $this->config->getOrganizationHost() . '/consumer/' . $uuid->toString() . '/dossier';
    }

    /**
     * @return string
     */
    public function getOrganizationConsumersUrl(): string
    {
        return $this->config->getOrganizationHost() . '/consumers/';
    }
}
