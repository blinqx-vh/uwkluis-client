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

class SignRequest
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
                "{$this->config->getApiHost()}/sign-request?{$queryString}",
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

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $signRequestId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        Token $accessToken,
        string $consumerId,
        string $signRequestId
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/sign-request/{$signRequestId}?{$queryString}",
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
        int $signRequestId
    ): bool {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/sign-request/{$signRequestId}?{$queryString}",
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

    public function new(
        Token $accessToken,
        string $consumerId,
        string $name,
        string $document,
        string $signType,
        ?string $description = null,
        ?string $authMethod = null,
        ?string $assignedPerson = null,
        ?array $additionalSigners = null
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/sign-request/?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'name' => $name,
                        'description' => $description,
                        'document' => $document,
                        'sign_type' => $signType,
                        'auth_method' => $authMethod,
                        'assigned_person' => $assignedPerson,
                        'additional_signers' => $additionalSigners,
                    ]
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }
}