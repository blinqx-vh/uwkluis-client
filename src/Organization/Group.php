<?php
declare(strict_types=1);

namespace Ufo\Client\Organization;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ufo\Client\Traits\ProcessesBadResponses;

/**
 * Class Group
 */
final class Group
{
    use ProcessesBadResponses;

    /** @var Config */
    private $config;
    /** @var ClientInterface */
    private $guzzleClient;

    /**
     * Information constructor.
     *
     * @param Config          $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    ) {
        $this->config       = $config;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param Token $accessToken
     * @param string $groupId
     *
     * @return array
     * @throws GuzzleException
     */
    public function list(Token $accessToken, string $groupId): array
    {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                $this->config->getApiHost() . '/groups/' . $groupId,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
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
     * @param Token $accessToken
     * @param string $groupId
     * @param string $consumerId
     *
     * @return array
     * @throws GuzzleException
     */
    public function add(Token $accessToken, string $groupId, string $consumerId): array
    {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                $this->config->getApiHost() . '/groups/' . $groupId . '/consumer/' . $consumerId,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
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
     * @param Token $accessToken
     * @param string $groupId
     * @param string $consumerId
     *
     * @return array
     * @throws GuzzleException
     */
    public function remove(Token $accessToken, string $groupId, string $consumerId): array
    {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                $this->config->getApiHost() . '/groups/' . $groupId . '/consumer/' . $consumerId,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
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
}
