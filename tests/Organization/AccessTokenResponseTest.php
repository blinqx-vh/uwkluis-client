<?php

namespace UwKluis\Client\Organization;

use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;

class AccessTokenResponseTest extends TestCase
{

    public function testGetRefreshToken()
    {
        $accessToken = $this->createMock(Token::class);
        $response = new AccessTokenResponse(
            $accessToken,
            'foo',
            new \DateTime()
        );
        $this->assertSame($accessToken, $response->getAccessToken());
    }

    public function testGetAccessToken()
    {
        $response = new AccessTokenResponse(
            new Token(),
            'foo',
            new \DateTime()
        );
        $this->assertEquals('foo', $response->getRefreshToken());
    }

    public function testGetExpiration()
    {

        $expiration = new \DateTime();
        $response = new AccessTokenResponse(
            new Token(),
            'foo',
            $expiration
        );
        $this->assertSame($expiration, $response->getExpiration());
    }
}
