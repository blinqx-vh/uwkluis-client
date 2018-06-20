<?php
declare(strict_types = 1);

namespace Ufo\Client\Exception;

use Throwable;

/**
 * Class ConsumerConnectionConflict
 */
final class ConsumerConnectionConflict extends ConsumerConnectionException
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Throwable $previous [optional] The previous throwable used for the exception chaining.
     * @since 5.1.0
     */
    public function __construct(
        string $message = "Consumer with this email and phone number is already connected or invited",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
