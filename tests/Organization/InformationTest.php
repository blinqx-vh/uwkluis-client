<?php

namespace Ufo\Client\Organization;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class InformationTest
 */
class InformationTest extends TestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetScopes()
    {
        $guzzleClientMock = $this->getMockBuilder(Client::class)
            ->getMock();
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode(['email' => 'example@example.com', 'name' => 'example b.v.']))
            );
        /** @noinspection PhpParamsInspection */
        $information = new Information(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $guzzleClientMock);
        $this->assertEquals(
            ['email' => 'example@example.com', 'name' => 'example b.v.'],
            $information->getOrganizationInformation()
        );
    }
}
