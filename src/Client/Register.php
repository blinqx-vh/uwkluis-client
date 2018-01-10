<?php
declare(strict_types = 1);

namespace Ufo\Client\Client;

use GuzzleHttp\Client as GuzzleClient;
use Ufo\Client\Organization\Config;

/**
 * Class Register
 */
final class Register
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Register constructor.
     *
     * @param GuzzleClient $guzzleClient
     * @param Config       $config
     */
    public function __construct(GuzzleClient $guzzleClient, Config $config)
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function register(): string
    {
        $data = [
            'name'     => $this->config->getClientName(),
            'redirect' => $this->config->getCallbackUri(),
        ];

        return $this->guzzleClient->post($this->config->getOrganizationHost() . '/oauth/clients', $data)
            ->getBody()->getContents();
    }
}
