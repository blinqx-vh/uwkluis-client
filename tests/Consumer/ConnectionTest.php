<?php

namespace Ufo\Client\Consumer;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use UwKluis\Enums\ConsumerConnection\Status;

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
    public function testGetStatus()
    {
        $status = new Status(Status::NEW);
        $connection = new Connection(
            Uuid::uuid4(),
            $status
        );
        $this->assertEquals($status, $connection->getStatus());
    }

    /**
     * @throws \Exception
     */
    public function testGetGrantedScopes()
    {
        $connection = new Connection(
            Uuid::uuid4(),
            new Status(Status::NEW),
            ['foo']
        );
        $this->assertEquals(['foo'], $connection->getGrantedScopes());
    }
}
