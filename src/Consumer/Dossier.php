<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Exception\ConsumerConnectionException;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Exception\OrganizationConnectionException;
use Ufo\Client\Exception\ValidationException;
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
        $this->config       = $config;
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
        $query       = [
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
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
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
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }

    /**
     * @param BadResponseException $e
     */
    private function processBadResponse(BadResponseException $e)
    {
        $exceptionResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
        if (
            ($e->getCode() === StatusCodeInterface::STATUS_FORBIDDEN
             && $exceptionResponse === 'Invalid consumer connection'
            )
            || ($e->getCode() === StatusCodeInterface::STATUS_NOT_FOUND
                && $exceptionResponse === 'consumer connection not found'
            )
        ) {
            throw new ConsumerConnectionException($exceptionResponse, $e->getCode(), $e);
        }

        if ($e->getCode() === StatusCodeInterface::STATUS_UNAUTHORIZED) {
            throw new OrganizationConnectionException(
                'Invalid organization connection',
                StatusCodeInterface::STATUS_FORBIDDEN,
                $e
            );
        }

        if ($e->getCode() === StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY) {
            throw  (new ValidationException('Validation failed', $e->getCode(), $e))
                ->setValidationErrors(json_decode(json_decode($e->getResponse()->getBody()->getContents())->message));
        }

        throw new InvalidRequestException(
            $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
            $e->getResponse() ? $e->getResponse()->getStatusCode() : 0,
            $e
        );
    }
}
