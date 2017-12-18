<?php
declare(strict_types = 1);

namespace Ufo\Client\Connection;

final class Config
{
    /** @var int */
    private $clientId;
    /** @var string */
    private $callbackUri;
    /** @var array|scopes */
    private $scopes;
    /** @var string */
    private $clientSecret;

    /**
     * Config constructor.
     *
     * @param int    $clientId
     * @param string $callbackUrl
     * @param string $clientSecret
     * @param array  $scopes
     */
    public function __construct(
        int $clientId,
        string $callbackUrl,
        string $clientSecret,
        array $scopes
    ) {
        $this->clientId = $clientId;
        $this->callbackUri = $callbackUrl;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
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
}
