<?php
declare(strict_types=1);

namespace UwKluis\Client\Organization;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Client\UwkluisClientInterface;

/**
 * Class Information
 */
final class Information
{
    public function __construct(
        private readonly Config        $config,
        private readonly UwkluisClientInterface $uwkluisClient
    ) {
    }

    /**
     * returns an associative array with organization information.
     *
     * @param Token $accessToken
     *
     * @return array
     * @throws GuzzleException
     */
    public function whoAmI(Token $accessToken): array
    {
        return json_decode((string)$this->uwkluisClient->request(
            RequestMethodInterface::METHOD_GET,
            $this->config->getApiHost() . '/whoami',
            [
                RequestOptions::HEADERS     => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken->toString(),
                ],
            ]
        )->getBody()->getContents(), true);
    }
}
