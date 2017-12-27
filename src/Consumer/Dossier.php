<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use Ufo\Client\Connection\AccessTokenResponse;
use Ufo\Client\Connection\Config;
use Ufo\Client\Exception\InvalidRequestException;

final class Dossier
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Dossier constructor.
     *
     * @param GuzzleClient $guzzleClient
     * @param Config       $config
     */
    public function __construct(
        GuzzleClient $guzzleClient,
        Config $config
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    /**
     * @param AccessTokenResponse $accessToken
     * @param string              $consumerId
     * @param int                 $version
     *
     * @return mixed
     */
    public function getData(
        AccessTokenResponse $accessToken,
        string $consumerId,
        int $version
    ) {
        $query = [
            'consumer_id' => $consumerId,
            'version'     => $version,
        ];
        $queryString = http_build_query($query);
        try {
            $httpResponse =
                $this->guzzleClient->get($this->config->getApiHost() . 'api/dossier?' . $queryString,
                    [
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken->getAccessToken(),
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse()->getBody()->getContents(),
                $e->getResponse()->getStatusCode()
            );
        }

        return json_decode($httpResponse, true);
    }

    /**
     * @param AccessTokenResponse $accessToken
     * @param string              $consumerId
     * @param array               $dossierData
     * @param int                 $responseDataVersion
     *
     * @return mixed
     */
    public function updateData(
        AccessTokenResponse $accessToken,
        string $consumerId,
        array $dossierData,
        int $responseDataVersion
    ) {
        $queryString = [
            'consumer_id' => $consumerId,
            'version'     => $responseDataVersion,
        ];
        $queryString = http_build_query($queryString);
        try {
            $httpResponse =
                $this->guzzleClient->post($this->config->getApiHost() . 'api/dossier?' . $queryString,
                    [
                        'headers'     => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken->getAccessToken(),
                        ],
                        'form_params' => [
                            'dossier' => json_encode($dossierData),
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse()->getBody()->getContents(),
                $e->getResponse()->getStatusCode()
            );
        }

        return json_decode($httpResponse, true);
    }
}
