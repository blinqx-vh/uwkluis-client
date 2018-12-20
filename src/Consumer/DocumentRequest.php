<?php
declare(strict_types=1);

namespace Ufo\Client\Consumer;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Organization\Config;
use Ufo\Client\Traits\ProcessesBadResponses;

class DocumentRequest
{
    use ProcessesBadResponses;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Files constructor.
     *
     * @param Config $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    )
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }


    /**
     * @param Token $accessToken
     * @param string $consumerId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(
        Token $accessToken,
        string $consumerId
    )
    {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                'get',
                "{$this->config->getApiHost()}/document-request?{$queryString}",
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
     * @param Token $accessToken
     * @param string $consumerId
     * @param string $documentId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        Token $accessToken,
        string $consumerId,
        string $documentId
    )
    {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                'get',
                "{$this->config->getApiHost()}/document-request/{$documentId}?{$queryString}",
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
     * @param Token $accessToken
     * @param string $consumerId
     * @param string $documentId
     * @param array $documentData
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(
        Token $accessToken,
        string $consumerId,
        string $documentId,
        array $documentData
    )
    {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                'get',
                "{$this->config->getApiHost()}/document-request/{$documentId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$accessToken,
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'dossier' => json_encode($documentData),
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