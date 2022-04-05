<?php
declare(strict_types=1);


namespace UwKluis\Client\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Throwable;
use UwKluis\Client\Exception\InvalidRequestException;

trait ChecksResponseFlow
{
    /**
     * @param $function
     *
     * @param array $arguments
     * @throws \Exception
     */
    private function checkResponseFlow($function, ...$arguments)
    {
        $uuid = Uuid::uuid4();
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $apiClient = $this->getApiClient($mockGuzzleClient);
        $token = $this->createMock(Token::class);

        $this->assertEquals(
            ['foo'],
            call_user_func([$apiClient, $function], $token, $uuid->toString(), ...$arguments)
        );
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            call_user_func([$apiClient, $function], $token, $uuid->toString(), ...$arguments);
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @return Client
     */
    private function getMockGuzzleClient(): Client
    {
        $mockGuzzleClient = $this->createMock(Client::class);
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
