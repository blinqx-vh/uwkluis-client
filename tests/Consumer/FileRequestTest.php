<?php

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Throwable;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Organization\Config;

class FileRequestTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testList()
    {
        $this->checkResponseFlow('list');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGet()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertEquals(
            ['foo'],
            $this->getFileRequest($mockGuzzleClient)->get(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getFileRequest($mockGuzzleClient)->get(new Token(), 'foo', 'bar');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUpdate()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertEquals(
            ['foo'],
            $this->getFileRequest($mockGuzzleClient)->update(new Token(), 'foo', 'bar', 'baz')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getFileRequest($mockGuzzleClient)->update(new Token(), 'foo', 'bar', 'baz');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDelete()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertEquals(
            ['foo'],
            $this->getFileRequest($mockGuzzleClient)->delete(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getFileRequest($mockGuzzleClient)->delete(new Token(), 'foo', 'bar');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testCreate()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertEquals(
            ['foo'],
            $this->getFileRequest($mockGuzzleClient)->create(new Token(), 'foo', ['bar'])
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getFileRequest($mockGuzzleClient)->create(new Token(), 'foo', ['bar']);
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDownloadZippedFiles()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getFileRequest($mockGuzzleClient)->downloadZip(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getFileRequest($mockGuzzleClient)->downloadZip(new Token(), 'foo', 'bar');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @param $function
     *
     * @throws \Exception
     */
    private function checkResponseFlow($function)
    {
        $uuid = Uuid::uuid4();
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $fileRequest = $this->getFileRequest($mockGuzzleClient);

        $this->assertEquals(['foo'], call_user_func([$fileRequest, $function], new Token(), $uuid->toString()));
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            call_user_func([$fileRequest, $function], new Token(), $uuid->toString());
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @return MockObject
     */
    private function getMockGuzzleClient(): MockObject
    {
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode(['foo'])
            ));

        return $mockGuzzleClient;
    }

    /**
     * @param MockObject $mockGuzzleClient
     *
     * @return FileRequest
     */
    private function getFileRequest(MockObject $mockGuzzleClient): FileRequest
    {
        /** @noinspection PhpParamsInspection */
        $fileRequest = new FileRequest(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient
        );

        return $fileRequest;
    }
}
