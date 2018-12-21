<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

/**
 * Class Config
 */
final class Config
{
    /** @var int */
    private $clientId;
    /** @var string */
    private $callbackUri;
    /** @var array */
    private $scopes;
    /** @var string */
    private $clientSecret;
    /** @var string */
    private $clientName;
    /** @var string */
    private $apiHost = '';
    /** @var string */
    private $organizationHost = '';

    /**
     * Config constructor.
     *
     * @param string $clientName
     * @param string $callbackUrl
     * @param int    $clientId
     * @param string $clientSecret
     * @param array  $scopes
     */
    public function __construct(
        string $clientName,
        string $callbackUrl,
        int $clientId = null,
        string $clientSecret = null,
        array $scopes = []
    ) {
        $this->clientId = $clientId;
        $this->callbackUri = $callbackUrl;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->clientName = $clientName;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getCallbackUri(): string
    {
        return $this->callbackUri;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @return string
     */
    public function getApiHost(): string
    {
        return $this->apiHost;
    }

    /**
     * @param string $apiHost
     *
     * @return Config
     */
    public function setApiHost(string $apiHost): Config
    {
        $apiHost = $this->clearTrailingSlashesFromHosts($apiHost);

        $this->apiHost = $apiHost;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationHost(): string
    {
        return $this->organizationHost;
    }

    /**
     * @param string $organizationHost
     *
     * @return Config
     */
    public function setOrganizationHost(string $organizationHost)
    {
        $organizationHost = $this->clearTrailingSlashesFromHosts($organizationHost);

        $this->organizationHost = $organizationHost;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return string
     */
    private function clearTrailingSlashesFromHosts(string $host): string
    {
        if (substr($host, -1) === '/') {
            trigger_error('Host should not contain trailing /');
            $host = rtrim($host, '/');
        }

        return $host;
    }
}
