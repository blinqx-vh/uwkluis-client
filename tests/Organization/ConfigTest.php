<?php

namespace Ufo\Client\Organization;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function testGetClientName()
    {
        $config = new Config(
            'foo',
            'bar'
        );
        $this->assertEquals('foo', $config->getClientName());
    }


    public function testGetCallbackUri()
    {
        $config = new Config(
            'foo',
            'bar'
        );
        $this->assertEquals('bar', $config->getCallbackUri());
    }

    public function testGetClientSecret()
    {
        $config = new Config(
            'foo',
            'bar',
            1,
            'baz'

        );
        $this->assertEquals('baz', $config->getClientSecret());
    }

    public function testGetScopes()
    {
        $config = new Config(
            'foo',
            'bar'
        );
        $this->assertEquals([], $config->getScopes());
    }


    public function testGetClientId()
    {
        $config = new Config(
            'foo',
            'bar'
        );
        try {
            $config->getClientId();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        $config = new Config(
            'foo',
            'bar',
            1
        );
        $this->assertEquals(1, $config->getClientId());
    }

    public function testOrganizationHost()
    {
        $config = new Config(
            'foo',
            'bar'
        );
        $this->assertEquals('', $config->getOrganizationHost());
        $config->setOrganizationHost('baz');
        $this->assertEquals('baz', $config->getOrganizationHost());
    }

    public function testApiHost()
    {
        $config = new Config(
            'foo',
            'bar'
        );
        $this->assertEquals('', $config->getApiHost());
        $config->setApiHost('baz');
        $this->assertEquals('baz', $config->getApiHost());
    }
}
