<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization\Webhooks;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use Lcobucci\JWT\Token;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Organization\Config;

final class Manage
{
    /**
     * ConnectToAccount constructor.
     *
     * @param Config       $config
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(Config $config, GuzzleClient $guzzleClient)
    {
        $this->config = $config;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param Token $accessToken
     *
     * @return array
     */
    public function list(Token $accessToken): array
    {
        try {
            $httpResponse =
                $this->guzzleClient->get($this->config->getApiHost() . 'api/webhooks/webhook',
                    [
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse);
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @return array
     */
    public function post(Token $accessToken, string $targetUri, string $identifier)
    {
        $data = [
            'target_uri' => $targetUri,
            'identifier' => $identifier,
        ];
        try {
            $httpResponse =
                $this->guzzleClient->post(
                    $this->config->getApiHost() . 'api/webhooks/webhook',
                    [
                        'headers'     => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                        'form_params' => $data,
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse);
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @return array
     */
    public function get(Token $accessToken, int $id)
    {
        try {
            $httpResponse =
                $this->guzzleClient->get($this->config->getApiHost() . 'api/webhooks/webhook/' . $id,
                    [
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse);
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @return array
     */
    public function put(Token $accessToken, int $id, string $targetUri, string $identifier)
    {
        $query = [
            'target_uri' => $targetUri,
            'identifier' => $identifier,
        ];
        $queryString = http_build_query($query);
        try {
            $httpResponse =
                $this->guzzleClient->put(
                    $this->config->getApiHost() . 'api/webhooks/webhook/' . $id . '?' . $queryString,
                    [
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse);
    }

    /**
     * @param Token $accessToken
     * @param int   $id
     *
     * @return array
     */
    public function delete(Token $accessToken, int $id)
    {
        try {
            $httpResponse =
                $this->guzzleClient->delete($this->config->getApiHost() . 'api/webhooks/webhook/' . $id,
                    [
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }

        return json_decode($httpResponse);
    }

    /**
     * @param Token $accessToken
     */
    public function claimCheck(Token $accessToken)
    {
        try {
            $httpResponse =
                $this->guzzleClient->delete($this->config->getApiHost() . 'api/webhooks/webhook/' . $id,
                    [
                        'headers' => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . (string) $accessToken,
                        ],
                    ])->getBody()->getContents();
        } catch (BadResponseException $e) {
            throw new InvalidRequestException(
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'An unknown error has occurred',
                $e->getResponse() ? $e->getResponse()->getStatusCode() : 0
            );
        }
    }

}
