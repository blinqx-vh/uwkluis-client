<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Exception\ConsumerRequestException;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Exception\OrganizationRequestException;
use Ufo\Client\Organization\Config;

/**
 * Class Dossier
 */
final class Dossier
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Dossier constructor.
     *
     * @param Config       $config
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(
        Config $config,
        GuzzleClient $guzzleClient
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param int    $version
     *
     * @return array
     */
    public function getData(
        Token $accessToken,
        string $consumerId,
        int $version
    ): array {
        $query = [
            'consumer_id' => $consumerId,
            'version'     => $version,
        ];
        $queryString = http_build_query($query);
        try {
            $httpResponse =
                $this->guzzleClient->get(
                    $this->config->getApiHost() . '/dossier?' . $queryString,
                    [
                        RequestOptions::HEADERS => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ]
                )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $exceptionResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            if ($e->getCode() === 403 &&
                $exceptionResponse === 'Invalid consumer connection'
            ) {
                throw new ConsumerRequestException($exceptionResponse, 403);
            }

            if ($e->getCode() === 401) {
                throw new OrganizationRequestException('Invalid organization connection', 403);
            }

            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse, true);
    }

    /**
     * @param Token  $accessToken
     * @param string $consumerId
     * @param array  $dossierData
     * @param int    $responseDataVersion
     *
     * @return array
     */
    public function updateData(
        Token $accessToken,
        string $consumerId,
        array $dossierData,
        int $responseDataVersion
    ): array {
        $queryString = [
            'consumer_id' => $consumerId,
            'version'     => $responseDataVersion,
        ];
        $queryString = http_build_query($queryString);
        try {
            $httpResponse =
                $this->guzzleClient->post(
                    $this->config->getApiHost() . '/dossier?' . $queryString,
                    [
                        RequestOptions::HEADERS     => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                        RequestOptions::FORM_PARAMS => [
                            'dossier' => json_encode($dossierData),
                        ],
                    ]
                )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $exceptionResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            if ($e->getCode() === 403 &&
                $exceptionResponse === 'Invalid consumer connection'
            ) {
                throw new ConsumerRequestException($exceptionResponse, 403);
            }

            if ($e->getCode() === 401) {
                throw new OrganizationRequestException('Invalid organization connection', 403);
            }

            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse, true);
    }
}
