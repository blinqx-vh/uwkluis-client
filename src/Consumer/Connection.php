<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

/**
 * Class Connection
 */
final class Connection
{
    /** @var string */
    private $organizationConsumerId;
    /** @var string */
    private $ufoConsumerId;
    /** @var array|null */
    private $grantedScopes;
    /**
     * @var null|string
     */
    private $connectionCode1;
    /**
     * @var null|string
     */
    private $connectionCode2;

    /**
     * Connection constructor.
     *
     * @param string      $organizationConsumerId
     * @param string      $ufoConsumerId
     * @param string|null $connectionCode1
     * @param string|null $connectionCode2
     * @param array|null  $grantedScopes
     */
    public function __construct(
        string $organizationConsumerId,
        string $ufoConsumerId,
        string $connectionCode1 = null,
        string $connectionCode2 = null,
        array $grantedScopes = null
    ) {
        $this->organizationConsumerId = $organizationConsumerId;
        $this->ufoConsumerId = $ufoConsumerId;
        $this->connectionCode1 = $connectionCode1;
        $this->connectionCode2 = $connectionCode2;
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
    public function getConnectionCode1()
    {
        return $this->connectionCode1;
    }

    /**
     * @return null|string
     */
    public function getConnectionCode2()
    {
        return $this->connectionCode2;
    }

    /**
     * @return array|null
     */
    public function getGrantedScopes()
    {
        return $this->grantedScopes;
    }
}
