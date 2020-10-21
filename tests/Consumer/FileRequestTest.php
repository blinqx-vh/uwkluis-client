<?php

namespace UwKluis\Client\Consumer;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Throwable;
use UwKluis\Client\Exception\InvalidRequestException;
use UwKluis\Client\Organization\Config;

class FileRequestTest extends TestCase
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
    public function testGet()
    {
        $this->checkResponseFlow('get', 'foo');
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $this->checkResponseFlow('update', 'foo', 'bar');
    }

    /**
     * @throws Exception
     */
    public function testDelete()
    {
        $this->checkResponseFlow('delete', 'foo');
    }

    /**
     * @throws Exception
     */
    public function testCreate()
    {
        $this->checkResponseFlow('create', ['bar']);
    }

    /**
     * @throws GuzzleException
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
