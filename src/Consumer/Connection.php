<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use Ramsey\Uuid\UuidInterface;
use UwKluis\Enums\ConsumerConnection\Status;

/**
 * Class Connection
 */
final class Connection
{
    /** @var UuidInterface */
    private $ufoConsumerId;
    /** @var array|null */
    private $grantedScopes;
    /** @var Status */
    private $status;

    /**
     * Connection constructor.
     *
     * @param UuidInterface $ufoConsumerId
     * @param Status|null   $status
     * @param array|null    $grantedScopes
     */
    public function __construct(
        UuidInterface $ufoConsumerId,
        Status $status = null,
        array $grantedScopes = null
    ) {
        $this->ufoConsumerId = $ufoConsumerId;
        $this->status = $status;
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
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return array|null
     */
    public function getGrantedScopes()
    {
        return $this->grantedScopes;
    }
}
