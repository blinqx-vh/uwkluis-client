<?php
declare(strict_types=1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

class SingleSignOn
{
    use ProcessesBadResponses;

    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    )
    {
    }

    public function login(Token $accessToken, string $consumerId, string $reason)
    {
        try {
            $httpResponse =
                $this->uwkluisClient->request(
                    RequestMethodInterface::METHOD_POST,
                    $this->config->getApiHost() . '/sso',
                    [
                        RequestOptions::HEADERS => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken->toString(),
                        ],
                        RequestOptions::FORM_PARAMS => [
                            'consumer_id' => $consumerId,
                            'reason' => $reason,
                        ]
                    ]
                )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }
}
