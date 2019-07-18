<?php
declare(strict_types = 1);

namespace Ufo\Client\Exception;

use Throwable;

/**
 * Class RefreshTokenInvalidException
 */
final class RefreshTokenInvalidException extends InvalidRequestException
{
    /**
     * RefreshTokenInvalidException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'The refresh token is invalid.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
