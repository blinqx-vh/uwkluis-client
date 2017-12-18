<?php
declare(strict_types = 1);

namespace Ufo\Client\Connection;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Ufo\Client\Exception\InvalidRequestException;

final class ConnectToAccount
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config  */
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

    public function getRedirectUrl() {
        $query = http_build_query([
            'client_id'     => $this->clientConfig->getClientId(),
            'redirect_uri'  => $this->clientConfig->getCallbackUri(),
            'scope'         => implode(' ', $this->clientConfig->getScopes()),
            'response_type' => 'code',
        ]);

        return 'http://organization.ufo.local/oauth/authorize?' . $query;
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
            throw new \RuntimeException($parameters['error']);
        }
        $code = $parameters['code'];
        return $this->requestAccessToken($code);
    }

    /**
     * @param string $code
     *
     * @return AccessTokenResponse
     */
    private function requestAccessToken(string $code): AccessTokenResponse
    {
        $response = $this->guzzleClient->post('http://organization.ufo.local/oauth/token',
            [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $this->clientConfig->getClientId(),
                    'client_secret' => $this->clientConfig->getClientSecret(),
                    'redirect_uri'  => $this->clientConfig->getCallbackUri(),
                    'code'          => $code,
                ],
            ]
        )->getBody()->getContents();
        $data = json_decode($response, true);
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
        $expires = (new \DateTime())->add(new \DateInterval( 'PT' . $data['expires_in']  . 'S'));
        return new AccessTokenResponse(
            $data['access_token'],
            $data['refresh_token'],
            $expires
        );
    }
}
