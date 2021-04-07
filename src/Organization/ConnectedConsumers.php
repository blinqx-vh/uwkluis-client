<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;

final class ConnectedConsumers
{
    /** @var Config */
    private $config;
    /** @var ClientInterface */
    private $guzzleClient;

    /**
     * Information constructor.
     *
     * @param Config          $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    ) {
        $this->config       = $config;
        $this->guzzleClient = $guzzleClient;
    }

    public function connectedConsumers(Token $accessToken): array
    {
        return json_decode((string) $this->guzzleClient->request(
            RequestMethodInterface::METHOD_GET,
            $this->config->getApiHost() . '/connected-consumers',
            [
                RequestOptions::HEADERS     => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . (string) $accessToken,
                ],
            ]
        )->getBody()->getContents(), true);
    }
}
