<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\UploadedFileInterface;
use Ufo\Client\Organization\Config;
use Ufo\Client\Traits\ProcessesBadResponses;

/**
 * Class Files
 */
final class Files
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
     * Lists the files shared by the consumer
     *
     * @param Token  $accessToken
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
                'get',
                "{$this->config->getApiHost()}/files?{$queryString}",
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
     * lists the files shares by you with the consumer
     *
     * @param Token  $accessToken
     * @param string $consumerId
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listShared(
        Token $accessToken,
        string $consumerId
    ): array {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->request(
                'get',
                "{$this->config->getApiHost()}/files/shared?{$queryString}",
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
     * @param string $fileId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download(
        Token $accessToken,
        string $consumerId,
        string $fileId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->guzzleClient->request(
                'get',
                "{$this->config->getApiHost()}/files/{$fileId}?{$queryString}",
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
     * @param Token                 $accessToken
     * @param string                $consumerId
     * @param UploadedFileInterface $uploadedFile
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload(
        Token $accessToken,
        string $consumerId,
        UploadedFileInterface $uploadedFile
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->send(
                (new ServerRequest(
                    'post',
                    "{$this->config->getApiHost()}/files/?{$queryString}",
                    [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ]
                )
                )->withUploadedFiles([$uploadedFile])
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse->getBody()->getContents(), true);
    }
}
