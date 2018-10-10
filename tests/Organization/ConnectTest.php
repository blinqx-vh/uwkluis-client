<?php
declare(strict_types = 1);


namespace Ufo\Client\Organization;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Ufo\Client\Exception\AuthCodeExpiredException;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Exception\InvalidScopesException;
use Ufo\Client\Exception\RefreshTokenInvalidException;

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
                    'error'   => 'foo',
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
        }
        try {
            $connect->processResponse(new Request('get', 'foo?bar=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
        }
        try {
            $connect->processResponse(new Request('get', 'foo?error=foo'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertEquals(InvalidRequestException::class, get_class($e));
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(RefreshTokenInvalidException::class, $e);
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(AuthCodeExpiredException::class, $e);
        }
        try {
            $connect->processResponse(new Request('get', 'foo?code=baz'));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(AuthCodeExpiredException::class, $e);
        }
    }
}
