<?php

namespace Ufo\Client\Consumer;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ConnectionTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testGetUfoConsumerId()
    {
        $uuid = Uuid::uuid4();
        $connection = new Connection(
            $uuid
        );
        $this->assertSame($uuid, $connection->getUfoConsumerId());
    }

    /**
     * @throws \Exception
     */
    public function testGetGrantedScopes()
    {
        $connection = new Connection(
            Uuid::uuid4(),
            ['foo']
        );
        $this->assertEquals(['foo'], $connection->getGrantedScopes());
    }
}
