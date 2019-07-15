<?php
declare(strict_types=1);


namespace Ufo\Client\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Throwable;
use Ufo\Client\Exception\InvalidRequestException;

trait ChecksResponseFlow
{
    /**
     * @param $function
     *
     * @throws \Exception
     */
    private function checkResponseFlow($function)
    {
        $uuid = Uuid::uuid4();
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $files = $this->getApiClient($mockGuzzleClient);

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
     * @return MockObject|ClientInterface
     */
    private function getMockGuzzleClient(): MockObject
    {
        /** @var MockObject $mockGuzzleClient */
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

    abstract public function getApiClient(ClientInterface $client);
}
