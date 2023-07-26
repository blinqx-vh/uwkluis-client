<?php
declare(strict_types=1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Organization\Config;
use GuzzleHttp\ClientInterface;
use UwKluis\Client\Traits\ProcessesBadResponses;

class QuestionnaireType
{
    use ProcessesBadResponses;

    private ClientInterface $guzzleClient;
    private Config $config;

    public function __construct(
        Config          $config,
        ClientInterface $guzzleClient
    ) {
        $this->config = $config;
        $this->guzzleClient = $guzzleClient;
    }

    public function list(
        Token $accessToken
    ): array {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/questionnaires/types",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
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
        Token  $accessToken,
        string $questionnaireTypeId
    ): array {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/questionnaires/types/{$questionnaireTypeId}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    public function create(
        Token  $accessToken,
        array  $body
    ): array {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/questionnaires/types",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'body' => $body
                    ]
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    public function update(
        Token  $accessToken,
        string $questionnaireTypeId,
        array  $body
    ): array {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_PUT,
                "{$this->config->getApiHost()}/questionnaires/types/{$questionnaireTypeId}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'body' => $body
                    ]
                ],
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    public function delete(
        Token  $accessToken,
        string $questionnaireTypeId
    ): bool {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/questionnaires/types/{$questionnaireTypeId}",
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

        return $httpResponse->getStatusCode() === 204;
    }
}
