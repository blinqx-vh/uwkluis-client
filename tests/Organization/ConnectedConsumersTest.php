<?php

namespace UwKluis\Client\Organization;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;

/**
 * Class InformationTest
 */
class ConnectedConsumersTest extends TestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testWhoAmI()
    {
        $guzzleClientMock = $this->getMockBuilder(Client::class)
            ->getMock();
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode(['email' => 'example@example.com', 'phone_number' => '0611111111', 'ufo_consumer_external_id' => '4e939746-43ed-47a6-8b97-2bff52fa6492']))
            );
        /** @noinspection PhpParamsInspection */
        $connectedConsumers = new ConnectedConsumers(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $guzzleClientMock);
        $this->assertEquals(
            ['email' => 'example@example.com', 'phone_number' => '0611111111', 'ufo_consumer_external_id' => '4e939746-43ed-47a6-8b97-2bff52fa6492'],
            $connectedConsumers->connectedConsumers(new Token())
        );
    }
}
