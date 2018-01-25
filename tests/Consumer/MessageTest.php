<?php

namespace Ufo\Client\Consumer;

use PHPUnit\Framework\TestCase;
use Ufo\Client\Organization\Config;

class MessageTest extends TestCase
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
    public function testGet()
    {
        $this->checkResponseFlow('get', 'foo');
    }

    /**
     * @throws \Exception
     */
    public function testCreate()
    {
        $this->checkResponseFlow('create', ['foo']);
    }

    /**
     * @throws \Exception
     */
    public function testDelete()
    {
        $this->checkResponseFlow('delete', 'foo');
    }

    /**
     * @param $client
     * @return Message
     */
    private function getApiClient($client)
    {
        return new Message(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $client
        );
    }
}
