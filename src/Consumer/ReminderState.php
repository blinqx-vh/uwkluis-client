<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

final class ReminderState
{
    use ProcessesBadResponses;

    private ClientInterface $guzzleClient;
    private Config $config;

    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    public function get(
        Token $accessToken,
        string $consumerId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/reminder-state/?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    public function reset(
        Token $accessToken,
        string $consumerId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/reminder-state/reset?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }
}
