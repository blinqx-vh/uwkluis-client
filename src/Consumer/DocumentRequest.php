<?php
declare(strict_types=1);

namespace Ufo\Client\Consumer;


use GuzzleHttp\ClientInterface;
use Ufo\Client\Organization\Config;
use Ufo\Client\Traits\ProcessesBadResponses;

class DocumentRequest
{
    use ProcessesBadResponses;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Files constructor.
     *
     * @param Config $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    )
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }
}