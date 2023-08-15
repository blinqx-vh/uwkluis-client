<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\UploadedFileInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

/**
 * Class FileType
 */
final class FileType
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
     * @param Token $accessToken
     * @param string $consumerId
     * @param int $documentTypeId
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function approve(
        Token $accessToken,
        string $consumerId,
        int $documentTypeId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_PUT,
                "{$this->config->getApiHost()}/document-types/{$documentTypeId}/approve?{$queryString}",
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
        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse->getBody()->getContents(), true);
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $documentTypeId
     * @param string $reason
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unapprove(
        Token $accessToken,
        string $consumerId,
        string $documentTypeId,
        string $reason
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_PUT,
                "{$this->config->getApiHost()}/document-types/{$documentTypeId}/unapprove?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'reason' => $reason,
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }
        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse->getBody()->getContents(), true);
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $documentTypeId
     * @param string $description
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function editDescription(
        Token $accessToken,
        string $consumerId,
        string $documentTypeId,
        string $description
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_PUT,
                "{$this->config->getApiHost()}/document-types/{$documentTypeId}/edit-description?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'description' => $description,
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }
        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse->getBody()->getContents(), true);
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $documentTypeId
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId,
        string $documentTypeId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}/document-type/{$documentTypeId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ]
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }
        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse->getBody()->getContents(), true);
    }

    /**
     * @param Token                 $accessToken
     * @param string                $consumerId
     * @param string                $fileRequestId
     * @param string                $documentTypeId
     * @param UploadedFileInterface $uploadedFile
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId,
        string $documentTypeId,
        UploadedFileInterface $uploadedFile
    ): array {
        $queryString = http_build_query(['consumer_id' => $consumerId]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}/document-type/{$documentTypeId}/upload?{$queryString}",
                [
                    RequestOptions::HEADERS   => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::MULTIPART => [
                        [
                            'Content-type' => 'multipart/form-data',
                            'name'         => 'file',
                            'contents'     => $uploadedFile->getStream(),
                            'filename'     => $uploadedFile->getClientFilename(),
                        ],
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse->getBody()->getContents(), true);
    }

    public function deleteFile(
        Token $accessToken,
        string $consumerId,
        string $fileRequestId,
        string $documentTypeId,
        string $fileId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/files/request/{$fileRequestId}/document-type/{$documentTypeId}/file/{$fileId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ]
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse->getBody()->getContents(), true);
    }
}
