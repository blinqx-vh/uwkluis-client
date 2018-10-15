<?php

namespace Ufo\Client\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Throwable;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Organization\Config;

class FilesTest extends TestCase
{
    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testList()
    {
        $this->checkResponseFlow('list');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testListShared()
    {
        $this->checkResponseFlow('listShared');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDownload()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getFiles($mockGuzzleClient)->download(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getFiles($mockGuzzleClient)->download(new Token(), 'foo', 'bar');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUpload()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $mockGuzzleClient->expects($this->any())
            ->method('send')
            ->willReturn(new Response(
                200,
                [],
                json_encode(['foo'])
            ));

        $this->assertEquals(
            ['foo'],
            $this->getFiles($mockGuzzleClient)->upload(
                new Token(),
                'foo',
                ServerRequest::normalizeFiles([
                    [
                        'tmp_name' => 'bar',
                        'size'     => 'bar',
                        'error'    => 'bar',
                        'name'     => 'bar',
                        'type'     => 'bar',
                    ]
                ])[0],
                'foo',
                'bar'
            )
        );

        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $response = $this->getFiles($mockGuzzleClient)->upload(
                new Token(),
                'foo',
                ServerRequest::normalizeFiles([
                    [
                        'tmp_name' => 'bar',
                        'size'     => 'bar',
                        'error'    => 'bar',
                        'name'     => 'bar',
                        'type'     => 'bar',
                    ]
                ])[0],
                'foo',
                'bar'
            );
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
        $files = $this->getFiles($mockGuzzleClient);

        $this->assertEquals(['foo'], call_user_func([$files, $function], new Token(), $uuid->toString()));
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            call_user_func([$files, $function], new Token(), $uuid->toString());
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
     * @return Files
     */
    private function getFiles(MockObject $mockGuzzleClient): Files
    {
        /** @noinspection PhpParamsInspection */
        $files = new Files(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient
        );

        return $files;
    }
}
