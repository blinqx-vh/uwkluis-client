<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

class FinlyForm
{
    use ProcessesBadResponses;

    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
    }

    public function list(
        Token $accessToken,
        string $consumerId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/finly-form?{$queryString}",
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
        string $finlyFormId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/finly-form/{$finlyFormId}?{$queryString}",
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
        string $finlyFormId
    ): bool {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/finly-form/{$finlyFormId}?{$queryString}",
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
        string $name,
        int $finlyClientId,
        int $finlyFormId,
        int $finlyForminstanceId,
        ?string $completedAt = null
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/finly-form/?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'name' => $name,
                        'finly_client_id' => $finlyClientId,
                        'finly_form_id' => $finlyFormId,
                        'finly_forminstance_id' => $finlyForminstanceId,
                        'completed_at' => $completedAt,
                    ]
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    public function complete(
        Token $accessToken,
        string $consumerId,
        string $finlyFormId,
        string $completedAt
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_PATCH,
                "{$this->config->getApiHost()}/finly-form/{$finlyFormId}/complete?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'completed_at' => $completedAt,
                    ]
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }
}
