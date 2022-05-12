<?php

namespace UwKluis\Client\Consumer;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use UwKluis\Client\Exception\InvalidRequestException;
use UwKluis\Client\Organization\Config;

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
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getApiClient($mockGuzzleClient)->download($token, 'foo', 'bar')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->download($token, 'foo', 'bar');
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
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->getApiClient($mockGuzzleClient)->downloadZippedFiles($token, 'foo')
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->downloadZippedFiles($token, 'foo');
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
     * @param Client $mockGuzzleClient
     * @return Files
     */
    public function getApiClient(Client $mockGuzzleClient): Files
    {
        return new Files(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient
        );
    }
}
