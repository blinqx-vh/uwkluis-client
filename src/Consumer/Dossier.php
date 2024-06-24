<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

/**
 * Class Dossier
 */
final class Dossier
{
    use ProcessesBadResponses;

    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
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
                $this->uwkluisClient->request(
                    RequestMethodInterface::METHOD_GET,
                    $this->config->getApiHost() . '/dossier?' . $queryString,
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
     * @param array  $dossierData
     * @param int    $responseDataVersion
     * @param bool   $presave
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateData(
        Token $accessToken,
        string $consumerId,
        array $dossierData,
        int $responseDataVersion,
        bool $presave = false
    ): array {
        $queryString = [
            'consumer_id' => $consumerId,
            'presave'     => $presave ? 1 : 0,
            'version'     => $responseDataVersion,
        ];
        $queryString = http_build_query($queryString);
        try {
            $httpResponse =
                $this->uwkluisClient->request(
                    RequestMethodInterface::METHOD_POST,
                    $this->config->getApiHost() . '/dossier?' . $queryString,
                    [
                        RequestOptions::HEADERS     => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken->toString(),
                        ],
                        RequestOptions::FORM_PARAMS => [
                            'dossier' => json_encode($dossierData),
                        ],
                    ]
                )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return json_decode($httpResponse, true);
    }
}
