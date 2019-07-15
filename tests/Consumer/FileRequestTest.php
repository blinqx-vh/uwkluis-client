<?php

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
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
    use ChecksResponseFlow;

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
            $this->getApiClient($mockGuzzleClient)->get(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->get(new Token(), 'foo', 'bar');
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
            $this->getApiClient($mockGuzzleClient)->update(new Token(), 'foo', 'bar', 'baz')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->update(new Token(), 'foo', 'bar', 'baz');
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
            $this->getApiClient($mockGuzzleClient)->delete(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->delete(new Token(), 'foo', 'bar');
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
            $this->getApiClient($mockGuzzleClient)->create(new Token(), 'foo', ['bar'])
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->create(new Token(), 'foo', ['bar']);
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
            $this->getApiClient($mockGuzzleClient)->downloadZip(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->downloadZip(new Token(), 'foo', 'bar');
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
        $fileRequest = $this->getApiClient($mockGuzzleClient);

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
     * @param ClientInterface $client
     * @return FileRequest
     */
    public function getApiClient(ClientInterface $client)
    {
        /** @noinspection PhpParamsInspection */
        return new FileRequest(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $client
        );
    }
}
