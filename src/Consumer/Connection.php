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
    private $uwKluisConsumerId;
    /** @var array|null */
    private $grantedScopes;
    /** @var Status */
    private $status;

    /**
     * Connection constructor.
     *
     * @param UuidInterface $uwKluisConsumerId
     * @param Status|null   $status
     * @param array|null    $grantedScopes
     */
    public function __construct(
        UuidInterface $uwKluisConsumerId,
        Status $status = null,
        array $grantedScopes = null
    ) {
        $this->uwKluisConsumerId = $uwKluisConsumerId;
        $this->status = $status;
        $this->grantedScopes = $grantedScopes;
    }


    /**
     * @return UuidInterface
     */
    public function getUwKluisConsumerId(): UuidInterface
    {
        return $this->uwKluisConsumerId;
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
