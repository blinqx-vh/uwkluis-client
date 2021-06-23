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

/**
 * Class FileRequest
 */
class FileRequest
{
    use ProcessesBadResponses;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Files constructor.
     *
     * @param Config          $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }


    /**
     * @param Token  $accessToken
     * @param string $consumerId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(
        Token $accessToken,
        string $consumerId
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/request?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
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
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $fileRequestId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
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
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $fileRequestId
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadZip(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId
    ): ResponseInterface {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $response = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}/zip?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return $response;
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $fileRequestId
     * @param string $status
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId,
        string $status
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_PUT,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}?{$queryString}",
                [
                    RequestOptions::HEADERS     => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'status' => $status,
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
     * @param Token  $accessToken
     * @param string $consumerId
     * @param array  $fileData
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(
        Token $accessToken,
        string $consumerId,
        array $fileData
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/files/request?{$queryString}",
                [
                    RequestOptions::HEADERS     => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'body' => json_encode($fileData),
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
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $fileRequestId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
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
     * @param Token $accessToken
     * @param string $consumerId
     * @param string $fileRequestId
     * @param array $documentTypeData
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addDocumentType(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId,
        array $documentTypeData
    ) {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}/document-type?{$queryString}",
                [
                    RequestOptions::HEADERS     => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                    RequestOptions::FORM_PARAMS => $documentTypeData,
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }
}
