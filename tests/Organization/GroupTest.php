<?php

namespace Ufo\Client\Organization;

use PHPUnit\Framework\TestCase;
use Ufo\Client\Consumer\ChecksResponseFlow;

class GroupTest extends TestCase
{
    use ChecksResponseFlow;

    /**
     * @throws \Exception
     */
    public function testList()
    {
        $this->checkResponseFlow('list');
    }

    /**
     * @throws \Exception
     */
    public function testAdd()
    {
        $this->checkResponseFlow('add', 'foo', 'bar', 'baz');
    }

    /**
     * @throws \Exception
     */
    public function testRemove()
    {
        $this->checkResponseFlow('remove', 'foo', 'bar', 'baz');
    }

    /**
     * @param $client
     * @return Group
     */
    private function getApiClient($client)
    {
        return new Group(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $client
        );
    }
}
