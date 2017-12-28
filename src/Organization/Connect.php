<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

use DateInterval;
use DateTime;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use Lcobucci\JWT\Parser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Ufo\Client\Exception\InvalidRequestException;

final class Connect
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $clientConfig;

    /**
     * ConnectToAccount constructor.
     *
     * @param Config       $clientConfig
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(Config $clientConfig, GuzzleClient $guzzleClient)
    {
        $this->clientConfig = $clientConfig;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        $query = http_build_query([
            'client_id'     => $this->clientConfig->getClientId(),
            'redirect_uri'  => $this->clientConfig->getCallbackUri(),
            'scope'         => implode(' ', $this->clientConfig->getScopes()),
            'response_type' => 'code',
        ]);

        return $this->clientConfig->getApiHost() . 'oauth/authorize?' . $query;
    }

    /**
     * @param RequestInterface $request
     *
     * @return AccessTokenResponse
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
     */
    public function refreshAccessToken(string $refreshToken): AccessTokenResponse
    {
        try {
            $response = $this->guzzleClient->post($this->clientConfig->getApiHost() . '/token/refresh',
                [
                    'form_params' => [
                        'grant_type'    => 'refresh_token',
                        'refresh_token' => $refreshToken,
                        'client_id'     => $this->clientConfig->getClientId(),
                        'client_secret' => $this->clientConfig->getClientSecret(),
                        'scope'         => implode(' ', $this->clientConfig->getScopes()),
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
     */
    private function requestAccessToken(string $code): AccessTokenResponse
    {
        try {
            $response = $this->guzzleClient->post($this->clientConfig->getApiHost() . '/oauth/token',
                [
                    'form_params' => [
                        'grant_type'    => 'authorization_code',
                        'client_id'     => $this->clientConfig->getClientId(),
                        'client_secret' => $this->clientConfig->getClientSecret(),
                        'redirect_uri'  => $this->clientConfig->getCallbackUri(),
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
     */
    private function processTokenResponse(ResponseInterface $response): AccessTokenResponse
    {
        $content = $response->getBody()->getContents();
        $statusCode = $response->getStatusCode();
        $data = json_decode($content, true);

        if (isset($data['error']) && $data['error'] === 'invalid_request') {
            $message = '';
            if (isset($data['message'])) {
                $message .= $data['message'];
            }
            if (isset($data['hint']) && $data['hint'] === 'Authorization code has expired') {
                $message .= ' - ' . $data['hint'];
            }
            throw new InvalidRequestException($message);
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
}
