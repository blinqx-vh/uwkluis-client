<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

use DateInterval;
use DateTime;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Parser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Ufo\Client\Exception\AuthCodeExpiredException;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Exception\InvalidScopesException;
use Ufo\Client\Exception\RefreshTokenInvalidException;

/**
 * Class Connect
 */
final class Connect
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * ConnectToAccount constructor.
     *
     * @param Config       $config
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(
        Config $config,
        GuzzleClient $guzzleClient
    ) {
        $this->config = $config;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string
    {
        $query = http_build_query(
            [
                'client_id'     => $this->config->getClientId(),
                'redirect_uri'  => $this->config->getCallbackUri(),
                'scope'         => implode(' ', $this->config->getScopes()),
                'response_type' => 'code',
            ]
        );

        return $this->config->getOrganizationHost() . '/oauth/authorize?' . $query;
    }

    /**
     * @return string
     */
    public function getRevokeUrl(): string
    {
        return $this->config->getOrganizationHost()
            . '/applications/revoke/' . $this->config->getClientId();
    }

    /**
     * @param RequestInterface $request
     *
     * @return AccessTokenResponse
     * @throws \Exception
     */
    public function processResponse(RequestInterface $request): AccessTokenResponse
    {
        $query = $request->getUri()->getQuery();
        $queryParams = explode('&', $query);
        $parameters = [];
        foreach ($queryParams as $queryParam) {
            list($key, $val) = explode('=', $queryParam);
            $parameters[$key] = $val;
        }
        if (isset($parameters['error']) || !isset($parameters['code'])) {
            throw new RuntimeException($parameters['error']);
        }
        $code = $parameters['code'];

        return $this->requestAccessToken($code);
    }

    /**
     * @param string $refreshToken
     *
     * @return AccessTokenResponse
     * @throws \Exception
     */
    public function refreshAccessToken(string $refreshToken): AccessTokenResponse
    {
        try {
            $response = $this->guzzleClient->post(
                $this->config->getOrganizationHost() . '/oauth/token',
                [
                    RequestOptions::HEADERS     => [
                        'Accept' => 'application/json',
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'grant_type'    => 'refresh_token',
                        'refresh_token' => $refreshToken,
                        'client_id'     => $this->config->getClientId(),
                        'client_secret' => $this->config->getClientSecret(),
                        'scope'         => implode(' ', $this->config->getScopes()),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        return $this->processTokenResponse($response);
    }

    /**
     * @param string $code
     *
     * @return AccessTokenResponse
     * @throws \Exception
     */
    private function requestAccessToken(string $code): AccessTokenResponse
    {
        try {
            $response = $this->guzzleClient->post(
                $this->config->getOrganizationHost() . '/oauth/token',
                [
                    RequestOptions::HEADERS     => [
                        'Accept' => 'application/json',
                    ],
                    RequestOptions::FORM_PARAMS => [
                        'grant_type'    => 'authorization_code',
                        'client_id'     => $this->config->getClientId(),
                        'client_secret' => $this->config->getClientSecret(),
                        'redirect_uri'  => $this->config->getCallbackUri(),
                        'code'          => $code,
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        return $this->processTokenResponse($response);
    }

    /**
     * @param $response
     *
     * @return AccessTokenResponse
     * @throws \Exception
     */
    private function processTokenResponse(ResponseInterface $response): AccessTokenResponse
    {
        $content = $response->getBody()->getContents();
        $statusCode = $response->getStatusCode();
        $data = json_decode($content, true);

        if (isset($data['error'])) {
            $this->processError($data);
        }
        if ($statusCode < 400
            && isset($data['expires_in'], $data['access_token'], $data['refresh_token'])) {
            $expires = (new DateTime())->add(new DateInterval('PT' . $data['expires_in'] . 'S'));
            $accessToken = (new Parser())->parse($data['access_token']);
            $refreshToken = $data['refresh_token'];

            return new AccessTokenResponse(
                $accessToken,
                $refreshToken,
                $expires
            );
        }
        throw new InvalidRequestException('An unknown error has occurred.');
    }

    /**
     * @param array $data
     */
    private function processError(array $data)
    {
        $message = '';
        if ($data['error'] === 'invalid_scope') {
            if (isset($data['message'])) {
                $message .= $data['message'];

            }
            if (isset($data['hint'])) {
                $message .= ' - ' . $data['hint'];
            }
            throw new InvalidScopesException($message);
        }

        if ($data['error'] === 'invalid_request') {
            if (isset($data['message'])) {
                $message .= $data['message'];
                if ($message === 'The refresh token is invalid.') {
                    throw new RefreshTokenInvalidException($message);
                }
            }
            if (isset($data['hint']) && $data['hint'] === 'Authorization code has expired') {
                $message .= ' - ' . $data['hint'];
                throw new AuthCodeExpiredException($message);
            }
            throw new InvalidRequestException($message);
        }
    }
}
