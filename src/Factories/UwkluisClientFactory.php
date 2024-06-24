<?php
declare(strict_types=1);

namespace UwKluis\Client\Factories;

use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use UwKluis\Client\Client\UwkluisClient;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Exception\InvalidArgumentException;

class UwkluisClientFactory
{
    private const HTTP_BAD_GATEWAY = 502;
    private const HTTP_GATEWAY_TIMEOUT = 504;

    public function create(array $config = []): UwkluisClientInterface
    {
        $stack = $config['stack'] ?? HandlerStack::create();
        if (!$stack instanceof HandlerStack) {
            throw new InvalidArgumentException('Stack must be an instance of HandlerStack.');
        }

        $stack->push(GuzzleRetryMiddleware::factory([
            'max_retry_attempts' => 3,
            'retry_on_status' => [self::HTTP_BAD_GATEWAY, self::HTTP_GATEWAY_TIMEOUT],
        ]));

        $config['stack'] = $stack;

        return new UwkluisClient($config);
    }
}