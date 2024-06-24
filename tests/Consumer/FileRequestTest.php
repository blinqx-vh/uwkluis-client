<?php

namespace UwKluis\Client\Consumer;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use UwKluis\Client\Client\UwkluisClientInterface;
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
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                new Response(
                    StatusCodeInterface::STATUS_IM_A_TEAPOT,
                    [],
                    'foo'
                )
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->downloadZip($token, 'foo', 'bar');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }


    public function getApiClient(UwkluisClientInterface $client): FileRequest
    {
        return new FileRequest(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $client
        );
    }
}
