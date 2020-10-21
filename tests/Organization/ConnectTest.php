<?php
declare(strict_types = 1);


namespace UwKluis\Client\Organization;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use UwKluis\Client\Exception\AuthCodeExpiredException;
use UwKluis\Client\Exception\InvalidRequestException;
use UwKluis\Client\Exception\InvalidScopesException;
use UwKluis\Client\Exception\RefreshTokenInvalidException;

class ConnectTest extends TestCase
{

    public function testGetUrls()
    {
        /** @noinspection PhpParamsInspection */
        $connect = new Connect(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $this->getMockBuilder(Client::class)->getMock());

        $this->assertEquals('/oauth/authorize?client_id=1&redirect_uri=https%3A%2F%2Fexample.org%2Ftest%2F'
            . '&scope=baz+quu+quuz&response_type=code', $connect->getAuthorizeUrl());

        $this->assertEquals('/applications/revoke/1', $connect->getRevokeUrl());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testProcessResponse()
    {
        $guzzleClientMock = $this->getMockBuilder(Client::class)
            ->getMock();
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode([
                    'error'   => 'invalid_scope',
                    'message' => 'foo',
                    'hint'    => 'bar',
                ])),
                new Response(200, [], json_encode([
                    'error'   => 'invalid_request',
                    'message' => 'foo',
                    'hint'    => 'bar',
                ])),
                new Response(200, [], json_encode([
                    'error'   => 'invalid_request',
                    'message' => 'The refresh token is invalid.',
                    'hint'    => 'foo',
                ])),
                new Response(200, [], json_encode([
                    'error'   => 'invalid_request',
                    'message' => 'foo',
                    'hint'    => 'Authorization code has expired',
                ])),
                new Response(200, [], json_encode([
                    'error' => 'foo',
                ])),
                new Response(200, [], json_encode([
                    'expires_in'    => '1000',
                    'access_token'  => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
                        . '.eyJmb28iOiJiYXIifQ'
                        . '.sLoOvOXnOK490o8iHakkNCMmsMMUwrZK9prFvjqOtYI',
                    'refresh_token' => 'baz',
                ]))
            );
        /** @noinspection PhpParamsInspection */
        $connect = new Connect(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $guzzleClientMock);

        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidScopesException::class, $e);
            $this->assertEquals('foo - bar', $e->getMessage());
        }
        try {
            $connect->processResponse(new Request('get', 'foo?bar=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals('unexpected error', $e->getMessage());
        }
        try {
            $connect->processResponse(new Request('get', 'foo?error=foo'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertEquals('foo', $e->getMessage());
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertEquals(InvalidRequestException::class, get_class($e));
            $this->assertEquals('foo', $e->getMessage());
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(RefreshTokenInvalidException::class, $e);
            $this->assertEquals('The refresh token is invalid.', $e->getMessage());
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(AuthCodeExpiredException::class, $e);
            $this->assertEquals('foo - Authorization code has expired', $e->getMessage());
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
            $this->assertEquals('An unknown error has occurred.', $e->getMessage());
        }
        $goodResponse = $connect->processResponse(new Request('get', 'foo?code=baz'));
        $this->assertInstanceOf(AccessTokenResponse::class, $goodResponse);

        /** @noinspection PhpParamsInspection */
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willThrowException(
                (new BadResponseException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response()
                ))
            );
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
            $this->assertEquals('An unknown error has occurred.', $e->getMessage());
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testRefreshAccessToken()
    {
        $guzzleClientMock = $this->getMockBuilder(Client::class)
            ->getMock();

        /** @noinspection PhpParamsInspection */
        $connect = new Connect(new Config(
            'foo',
            'https://example.org/test/',
            1,
            'bar',
            ['baz', 'quu', 'quuz']
        ), $guzzleClientMock);

        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode([
                    'expires_in'    => '1000',
                    'access_token'  => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'
                        . '.eyJmb28iOiJiYXIifQ'
                        . '.sLoOvOXnOK490o8iHakkNCMmsMMUwrZK9prFvjqOtYI',
                    'refresh_token' => 'baz',
                ]))
            );

        $response = $connect->refreshAccessToken('foo');
        $this->assertInstanceOf(AccessTokenResponse::class, $response);
         /** @noinspection PhpParamsInspection */
        $guzzleClientMock->expects($this->any())
            ->method('request')
            ->willThrowException(
                (new BadResponseException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response()
                ))
            );
        try {
            $connect->refreshAccessToken('foo');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
            $this->assertEquals('An unknown error has occurred.', $e->getMessage());
        }
    }
}
