<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use DateInterval;
use DateTime;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Exception\AuthCodeExpiredException;
use UwKluis\Client\Exception\InvalidOauthClientException;
use UwKluis\Client\Exception\InvalidRequestException;
use UwKluis\Client\Exception\InvalidScopesException;
use UwKluis\Client\Exception\RefreshTokenInvalidException;

/**
 * Class Connect
 */
final class Connect
{
    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
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
     * @throws Exception
     * @throws GuzzleException
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
            throw new RuntimeException($parameters['error'] ?? 'unexpected error');
        }
        $code = $parameters['code'];

        return $this->requestAccessToken($code);
    }

    /**
     * @param string $refreshToken
     *
     * @return AccessTokenResponse
     * @throws Exception
     * @throws GuzzleException
     */
    public function refreshAccessToken(string $refreshToken): AccessTokenResponse
    {
        try {
            $response = $this->uwkluisClient->request(
                'post',
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
     * @throws Exception
     * @throws GuzzleException
     */
    private function requestAccessToken(string $code): AccessTokenResponse
    {
        try {
            $response = $this->uwkluisClient->request(
                'post',
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
     * @throws Exception
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
            && isset($data['expires_in'], $data['access_token'])) {
            $expires = (new DateTime())->add(new DateInterval('PT' . $data['expires_in'] . 'S'));
            $accessToken = (new Parser(new JoseEncoder()))->parse($data['access_token']);
            $refreshToken = isset($data['refresh_token']) ? $data['refresh_token'] : null;

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
        if ($data['error'] === 'invalid_scope') {
            $this->processInvalidScope($data);
        }

        if ($data['error'] === 'invalid_request') {
            $this->processInvalidRequest($data);
        }

        if ($data['error'] === 'invalid_client') {
            throw new InvalidOauthClientException('Oauth client is not valid');
        }

        throw new InvalidRequestException('An unknown error has occurred. ('.$data['error'].')');
    }

    /**
     * @param array $data
     */
    private function processInvalidScope(array $data)
    {
        $message = '';
        if (isset($data['message'])) {
            $message .= $data['message'];
        }
        if (isset($data['hint'])) {
            $message .= ' - ' . $data['hint'];
        }
        throw new InvalidScopesException($message);
    }

    /**
     * @param array $data
     */
    private function processInvalidRequest(array $data)
    {
        if (isset($data['message']) && $data['message'] === 'The refresh token is invalid.') {
            throw new RefreshTokenInvalidException();
        }
        if (isset($data['hint']) && $data['hint'] === 'Authorization code has expired') {
            throw new AuthCodeExpiredException($data['message']);
        }

        throw new InvalidRequestException($data['message'] ?? '');
    }
}
