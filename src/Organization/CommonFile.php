<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\UploadedFileInterface;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Traits\ProcessesBadResponses;

final class CommonFile
{
    use ProcessesBadResponses;

    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
    }

    public function list(Token $accessToken): array
    {
        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_GET,
                $this->config->getApiHost() . '/common-file',
                [
                    RequestOptions::HEADERS     => [
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

    public function delete(Token $accessToken, string $fileId): bool
    {
        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/common-file/$fileId",
                [
                    RequestOptions::HEADERS     => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return $httpResponse->getStatusCode() === StatusCodeInterface::STATUS_NO_CONTENT;
    }

    public function upload(
        Token $accessToken,
        UploadedFileInterface $uploadedFile,
        ?string $description = null
    ): array {
        try {
            $httpResponse = $this->uwkluisClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/common-file",
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
                            'name'     => 'description',
                            'contents' => $description,
                        ],
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }
}
