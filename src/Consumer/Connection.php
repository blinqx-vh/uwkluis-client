<?php
declare(strict_types = 1);

namespace Client\Consumer;

final class Connection
{
    /** @var string */
    private $organizationConsumerId;
    /** @var string */
    private $ufoConsumerId;
    /** @var null|string */
    private $connectionCode;
    /** @var array|null */
    private $grantedScopes;

    /**
     * Connection constructor.
     *
     * @param string      $organizationConsumerId
     * @param string      $ufoConsumerId
     * @param string|null $connectionCode
     * @param array|null  $grantedScopes
     */
    public function __construct(
        string $organizationConsumerId,
        string $ufoConsumerId,
        string $connectionCode = null,
        array $grantedScopes = null
    ) {
        $this->organizationConsumerId = $organizationConsumerId;
        $this->ufoConsumerId = $ufoConsumerId;
        $this->connectionCode = $connectionCode;
        $this->grantedScopes = $grantedScopes;
    }

    /**
     * @return string
     */
    public function getOrganizationConsumerId(): string
    {
        return $this->organizationConsumerId;
    }

    /**
     * @return string
     */
    public function getUfoConsumerId(): string
    {
        return $this->ufoConsumerId;
    }

    /**
     * @return null|string
     */
    public function getConnectionCode()
    {
        return $this->connectionCode;
    }

    /**
     * @return array|null
     */
    public function getGrantedScopes()
    {
        return $this->grantedScopes;
    }
}
