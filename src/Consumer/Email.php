<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Ramsey\Uuid\UuidInterface;
use UwKluis\Enums\ConsumerConnection\Status;

final class Email
{
    /** @var string */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
