<?php
declare(strict_types = 1);

namespace Ufo\Client\Client;

use GuzzleHttp\Client as GuzzleClient;
use Ufo\Client\Organization\Config;

final class Register
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Register constructor.
     *
     * @param Config       $clientConfig
     * @param GuzzleClient $guzzleClient
     */
    public function __construct(GuzzleClient $guzzleClient, Config $config)
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    public function register()
    {
        $data = [
            'name' => $this->config->getClientName(),
            'redirect' => $this->config->getCallbackUri(),
        ];

        return $this->guzzleClient->post($this->config->getApiHost() . '/oauth/clients', $data)
            ->getBody()->getContents();
    }
}
