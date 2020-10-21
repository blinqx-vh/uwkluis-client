<?php
declare(strict_types=1);

namespace UwKluis\Client\Organization;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;

/**
 * Class Information
 */
final class Information
{
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
     * returns an associative array with organization information.
     *
     * @param Token $accessToken
     *
     * @return array
     * @throws GuzzleException
     */
    public function whoAmI(Token $accessToken): array
    {
        return json_decode((string) $this->guzzleClient->request(
            RequestMethodInterface::METHOD_GET,
            $this->config->getApiHost() . '/whoami',
            [
                RequestOptions::HEADERS     => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . (string) $accessToken,
                ],
            ]
        )->getBody()->getContents(), true);
    }
}
