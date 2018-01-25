<?php

namespace Ufo\Client\Consumer;

use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Organization\Config;

class FilesTest extends TestCase
{
    use ChecksResponseFlow;

    /**
     * @throws Exception
     */
    public function testList()
    {
        $this->checkResponseFlow('list');
    }

    /**
     * @throws Exception
     */
    public function testListShared()
    {
        $this->checkResponseFlow('listShared');
    }

    /**
     * @throws GuzzleException
     */
    public function testDownload()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getApiClient($mockGuzzleClient)->download(new Token(), 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->download(new Token(), 'foo', 'bar');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testDelete()
    {
        $this->checkResponseFlow('delete', 'foo');
    }

    /**
     * @throws GuzzleException
     */
    public function testDownloadZippedFiles()
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getApiClient($mockGuzzleClient)->downloadZippedFiles(new Token(), 'foo')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->downloadZippedFiles(new Token(), 'foo');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function testUpload()
    {
        $this->checkResponseFlow(
            'upload',
            ServerRequest::normalizeFiles([
            [
                'tmp_name' => 'bar',
                'size' => 'bar',
                'error' => 'bar',
                'name' => 'bar',
                'type' => 'bar',
            ]
            ])[0],
            'foo',
            'bar'
        );
    }

    /**
     * @param MockObject $mockGuzzleClient
     * @return Files
     */
    public function getApiClient(MockObject $mockGuzzleClient)
    {
        /** @noinspection PhpParamsInspection */
        return new Files(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient
        );
    }
}
