<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use Lcobucci\JWT\Token;
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
                            'Authorization' => 'Bearer ' . (string) $accessToken,
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
                            'Authorization' => 'Bearer ' . (string) $accessToken,
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
