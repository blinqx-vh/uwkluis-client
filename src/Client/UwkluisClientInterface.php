<?php
declare(strict_types=1);

namespace UwKluis\Client\Client;

use GuzzleHttp\ClientInterface;

// Convenience interface for creating a new instance of the Guzzle HTTP client through dependency injection.
interface UwkluisClientInterface extends ClientInterface
{
}