<?php
declare(strict_types = 1);

namespace Ufo\Client\Exception;

use Throwable;

/**
 * Class AuthCodeExpiredException
 */
final class AuthCodeExpiredException extends InvalidRequestException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . ' - Authorization code has expired', $code, $previous);
    }

}
