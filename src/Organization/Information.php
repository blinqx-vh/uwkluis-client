<?php
declare(strict_types=1);

namespace Ufo\Client\Organization;

use GuzzleHttp\ClientInterface;

/**
 * Class Information
 */
final class Information
{
    /** @var Config */
    private $config;
    /** @var ClientInterface */
    private $client;

    /**
     * Information constructor.
     *
     * @param Config $config
     * @param ClientInterface $client
     */
    public function __construct(
        Config $config,
        ClientInterface $client
    ) {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * returns an associative array with organization information.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrganizationInformation(): array
    {
        $response = $this->client
            ->request('get', $this->config->getApiHost() . '/information')
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }
}
