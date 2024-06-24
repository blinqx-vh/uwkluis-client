<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Client\UwkluisClientInterface;

final class ConnectedConsumers
{
    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
    }

    public function connectedConsumers(Token $accessToken): array
    {
        return json_decode((string)$this->uwkluisClient->request(
            RequestMethodInterface::METHOD_GET,
            $this->config->getApiHost() . '/connected-consumers',
            [
                RequestOptions::HEADERS     => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken->toString(),
                ],
            ]
        )->getBody()->getContents(), true);
    }
}
