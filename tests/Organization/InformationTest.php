<?php

namespace UwKluis\Client\Organization;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use UwKluis\Client\Client\UwkluisClient;

/**
 * Class InformationTest
 */
class InformationTest extends TestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testWhoAmI()
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        /** @var Client $guzzleClientMock */
        $guzzleClientMock = $this->createMock(UwkluisClient::class);
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode(['organization_name' => 'organization', 'email' => 'example@example.com', 'name' => 'example b.v.']))
            );
        $information = new Information(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $guzzleClientMock);
        $this->assertEquals(
            ['organization_name' => 'organization', 'email' => 'example@example.com', 'name' => 'example b.v.'],
            $information->whoAmI($token)
        );
    }
}
