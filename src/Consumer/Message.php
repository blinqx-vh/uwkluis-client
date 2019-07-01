<?php
declare(strict_types=1);

namespace Ufo\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Organization\Config;
use Ufo\Client\Traits\ProcessesBadResponses;

/**
 * Class Message
 */
final class Message
{
    use ProcessesBadResponses;

    /**
     * @var Config
     */
    private $config;
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * Message constructor.
     * @param Config $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    ) {
        $this->config = $config;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Lists the messages.
     *
     * @param Token $accessToken
     * @param string $consumerId
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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
                "{$this->config->getApiHost()}/messages?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$accessToken,
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }

    /**
     * @param Token $token
     * @param string $consumerId
     * @param string $messageId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        Token $token,
        string $consumerId,
        string $messageId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/messages/{$messageId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$token,
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Token $token
     * @param string $consumerId
     * @param array $data
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(
        Token $token,
        string $consumerId,
        array $data
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/messages?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$token,
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'body' => json_encode($data),
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }

    /**
     * @param Token $token
     * @param string $consumerId
     * @param string $fileId
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(
        Token $token,
        string $consumerId,
        string $fileId
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/messages/{$fileId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$token,
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }
}
