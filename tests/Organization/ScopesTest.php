<?php

namespace UwKluis\Client\Organization;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ScopesTest extends TestCase
{

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetScopes()
    {
        /** @var Client $guzzleClientMock */
        $guzzleClientMock = $this->createMock(Client::class);
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode(['foo', 'bar', 'baz']))
            );
        /** @noinspection PhpParamsInspection */
        $scopes = new Scopes(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $guzzleClientMock);
        $this->assertEquals(['foo', 'bar', 'baz'], $scopes->getScopes());
    }
}
