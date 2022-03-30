<?php

namespace UwKluis\Client\Organization;

use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;

class AccessTokenResponseTest extends TestCase
{

    public function testGetRefreshToken()
    {
        /** @var Token $accessToken */
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
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $response = new AccessTokenResponse(
            $token,
            'foo',
            new \DateTime()
        );
        $this->assertEquals('foo', $response->getRefreshToken());
    }

    public function testGetExpiration()
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $expiration = new \DateTime();
        $response = new AccessTokenResponse(
            $token,
            'foo',
            $expiration
        );
        $this->assertSame($expiration, $response->getExpiration());
    }
}
