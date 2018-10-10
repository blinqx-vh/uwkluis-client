<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use Ramsey\Uuid\UuidInterface;

/**
 * Class Connection
 */
final class Connection
{
    /** @var UuidInterface */
    private $ufoConsumerId;
    /** @var array|null */
    private $grantedScopes;

    /**
     * Connection constructor.
     *
     * @param UuidInterface $ufoConsumerId
     * @param array|null    $grantedScopes
     */
    public function __construct(
        UuidInterface $ufoConsumerId,
        array $grantedScopes = null
    ) {
        $this->ufoConsumerId = $ufoConsumerId;
        $this->grantedScopes = $grantedScopes;
    }


    /**
     * @return UuidInterface
     */
    public function getUfoConsumerId(): UuidInterface
    {
        return $this->ufoConsumerId;
    }


    /**
     * @return array|null
     */
    public function getGrantedScopes()
    {
        return $this->grantedScopes;
    }
}
