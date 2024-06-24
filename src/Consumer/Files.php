<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

/**
 * Class Files
 */
final class Files
{
    use ProcessesBadResponses;

    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
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
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files?{$queryString}",
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
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/shared?{$queryString}",
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
     * @param string $fileId
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download(
        Token $accessToken,
        string $consumerId,
        string $fileId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/{$fileId}?{$queryString}",
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

        return $response;
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $documentTypeIds
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadByDocumentTypeIds(
        Token $accessToken,
        string $consumerId,
        string $documentTypeIds
    ) {
        $queryString = http_build_query(
            [
                'consumer_id' => $consumerId,
                'document_type_ids' => $documentTypeIds
            ]
        );
        try {
            $response = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/document-types?{$queryString}",
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

        return $response;
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $attachmentUuid
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadByOrganizationMessageAttachmentUuid(
        Token $accessToken,
        string $consumerId,
        string $attachmentUuid
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/attachment/{$attachmentUuid}?{$queryString}",
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

        return $response;
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadZippedFiles(
        Token $accessToken,
        string $consumerId
    ): ResponseInterface {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/files/zip?{$queryString}",
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
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/files?{$queryString}",
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
                        [
                            'name'     => 'name',
                            'contents' => $fileName,
                        ],
                        [
                            'name'     => 'description',
                            'contents' => $description,
                        ],
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse->getBody()->getContents(), true);
    }


    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param string $fileId
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(
        Token $accessToken,
        string $consumerId,
        string $fileId
    ) {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/files/{$fileId}?{$queryString}",
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

        return json_decode($httpResponse->getBody()->getContents(), true);
    }
}
