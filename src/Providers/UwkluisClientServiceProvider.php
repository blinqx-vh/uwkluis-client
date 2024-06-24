<?php
declare(strict_types=1);

namespace UwKluis\Client\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Factories\UwkluisClientFactory;

class UwkluisClientServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(UwkluisClientInterface::class, static function ($app): UwkluisClientInterface {
            /** @var UwkluisClientFactory $factory */
            $factory = $app->make(UwkluisClientFactory::class);

            return $factory->create();
        });
    }

    public function provides(): array
    {
        return [UwkluisClientInterface::class];
    }
}