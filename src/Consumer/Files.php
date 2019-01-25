<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
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
                RequestMethodInterface::METHOD_GET,
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
                RequestMethodInterface::METHOD_GET,
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
                RequestMethodInterface::METHOD_GET,
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
     * @param string                $fileName
     * @param string                $description
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload(
        Token $accessToken,
        string $consumerId,
        UploadedFileInterface $uploadedFile,
        string $fileName,
        string $description
    ) {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/files/",
                [
                    RequestOptions::HEADERS   => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                    RequestOptions::MULTIPART => [
                        [
                            'Content-type' => 'multipart/form-data',
                            'name'         => 'file',
                            'contents'     => $uploadedFile->getStream(),
                            'filename'     => $uploadedFile->getClientFilename(),
                        ],
                        [
                            'name'     => 'name',
                            'contents' => $fileName,
                        ],
                        [
                            'name'     => 'description',
                            'contents' => $description,
                        ],
                        [
                            'name'     => 'consumer_id',
                            'contents' => $consumerId,
                        ],
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse->getBody()->getContents(), true);
    }
}
