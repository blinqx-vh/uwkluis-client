<?php
declare(strict_types = 1);

namespace Ufo\Client\Connection;

use Psr\Http\Message\RequestInterface;

final class ConnectToAccount
{
    public function getRedirectUrl(
        int $clientId,
        string $callbackUri,
        array $scopes
    ) {
        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $callbackUri,
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
        ]);
        return 'http://organization.ufo.local/oauth/authorize?'. $query;
    }

    /**
     * @param RequestInterface $request
     *
     * @return string $code - A JWT string
     */
    public function processResponse(RequestInterface $request)
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
        return $parameters['code'];
    }
}
