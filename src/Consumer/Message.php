<?php
declare(strict_types=1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\ResponseInterface;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

/**
 * Class Message
 */
final class Message
{
    use ProcessesBadResponses;


    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
    }

    /**
     * Lists the messages.
     *
     * @param Token $accessToken
     * @param string $consumerId
     *
     * @return array
     * @throws GuzzleException
     */
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
                "{$this->config->getApiHost()}/messages?{$queryString}",
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

    /**
     * @param Token $accessToken
     * @param string $consumerId
     * @param string $messageUuid
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function get(
        Token $accessToken,
        string $consumerId,
        string $messageUuid
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/messages/{$messageUuid}?{$queryString}",
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

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Token $accessToken
     * @param string $consumerId
     * @param array $data
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function create(
        Token $accessToken,
        string $consumerId,
        array $data
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/messages?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::MULTIPART => $data,
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }

    /**
     * @param Token $accessToken
     * @param string $consumerId
     * @param string $messageUuid
     * @return mixed
     * @throws GuzzleException
     */
    public function delete(
        Token $accessToken,
        string $consumerId,
        string $messageUuid
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/messages/{$messageUuid}?{$queryString}",
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

    /**
     * @param Token $token
     * @param string $consumerId
     * @param string $messageUuid
     * @param string $attachmentUuid
     * @return mixed
     * @throws GuzzleException
     */
    public function deleteAttachment(
        Token $accessToken,
        string $consumerId,
        string $messageUuid,
        string $attachmentUuid
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/messages/{$messageUuid}/attachment/{$attachmentUuid}?{$queryString}",
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
}
