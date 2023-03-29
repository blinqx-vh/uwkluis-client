<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\ResponseInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

class SourceDataRequest
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

    public function list(
        Token $accessToken,
        string $consumerId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/source-data-request?{$queryString}",
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

    public function get(
        Token $accessToken,
        string $consumerId,
        string $sourceDataRequestId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/source-data-request/{$sourceDataRequestId}?{$queryString}",
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

    public function delete(
        Token $accessToken,
        string $consumerId,
        string $sourceDataRequestId
    ): bool {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/source-data-request/{$sourceDataRequestId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return $httpResponse->getStatusCode() === 200;
    }

    public function create(
        Token $accessToken,
        string $consumerId,
        string $title,
        string $sourceType,
        ?string $description = null
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/source-data-request/?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'title' => $title,
                        'description' => $description,
                        'source_type' => $sourceType,
                    ]
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    public function pdf(
        Token $accessToken,
        string $consumerId,
        string $sourceDataRequestId
    ): ResponseInterface {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/source-data-request/{$sourceDataRequestId}/pdf?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return $response;
    }

    public function zip(
        Token $accessToken,
        string $consumerId,
        string $sourceDataRequestId
    ): ResponseInterface {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/source-data-request/{$sourceDataRequestId}/zip?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return $response;
    }
}
