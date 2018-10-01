<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Organization\Config;
use Ufo\Client\Traits\ProcessesBadResponses;

/**
 * Class Files
 */
final class Files
{
    use ProcessesBadResponses;
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
     * Lists the files shared by the consumer
     *
     * @param Token  $accessToken
     * @param string $consumerId
     *
     * @return array
     */
    public function list(
        Token $accessToken,
        string $consumerId
    ): array {
        $query = [
            'consumer_id' => $consumerId,
        ];
        $queryString = http_build_query($query);
        try {
            $httpResponse =
                $this->guzzleClient->get(
                    $this->config->getApiHost() . '/files?' . $queryString,
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
     * lists the files shares by you with the consumer
     *
     * @param Token  $accessToken
     * @param string $consumerId
     *
     * @return array
     */
    public function listShared(
        Token $accessToken,
        string $consumerId
    ): array {
        $query = [
            'consumer_id' => $consumerId,
        ];
        $queryString = http_build_query($query);
        try {
            $httpResponse =
                $this->guzzleClient->get(
                    $this->config->getApiHost() . '/files/shared?' . $queryString,
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
     * @param string $fileId
     *
     * @return mixed
     */
    public function download(
        Token $accessToken,
        string $consumerId,
        string $fileId
    ) {
        $query = [
            'consumer_id' => $consumerId,
        ];
        $queryString = http_build_query($query);
        try {
            return $this->guzzleClient->get(
                    $this->config->getApiHost() . "/files/{$fileId}?" . $queryString,
                    [
                        RequestOptions::HEADERS => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ]
                );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }
    }
}
