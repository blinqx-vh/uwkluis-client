<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Organization\Config;
use Ufo\Client\Traits\ProcessesBadResponses;

/**
 * Class Dossier
 */
final class Dossier
{
    use ProcessesBadResponses;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Dossier constructor.
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
     * @param int    $version
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
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
                $this->guzzleClient->request(
                    'get',
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
     * @throws \GuzzleHttp\Exception\GuzzleException
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
                $this->guzzleClient->request(
                    'post',
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
}
