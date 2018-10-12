<?php
declare(strict_types = 1);

namespace Ufo\Client\Exception;

use Throwable;
use Ufo\Client\Consumer\Connection;

/**
 * Class ConsumerConnectionConflict
 */
final class ConsumerConnectionConflict extends ConsumerConnectionException
{
    /** @var Connection */
    private $conflictingConnection;

    /**
     * ConsumerConnectionConflict constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = "Consumer with this email and phone number is already connected or invited",
        int $code = 0,
        Throwable $previous = null,
        Connection $conflictingConnection = null
    ) {
        $this->conflictingConnection = $conflictingConnection;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Connection
     */
    public function getConflictingConnection(): Connection
    {
        return $this->conflictingConnection;
    }
}
