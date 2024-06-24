<?php

namespace UwKluis\Client\Consumer;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use stdClass;
use Throwable;
use UwKluis\Client\Client\UwkluisClient;
use UwKluis\Client\Client\UwkluisClientInterface;
use Ramsey\Uuid\UuidInterface;
use UwKluis\Client\Exception\ConsumerConnectionConflict;
use UwKluis\Client\Exception\ConsumerConnectionException;
use UwKluis\Client\Exception\OrganizationConnectionException;
use UwKluis\Client\Organization\Config;
use UwKluis\Enums\ConsumerConnection\Status;

class ConnectTest extends TestCase
{

    public function testGetOrganizationConsumersUrl()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        $connect = $this->getConnect($mockGuzzleClient);

        $this->assertEquals('baz/consumers/', $connect->getOrganizationConsumersUrl());
    }

    /**
     * @throws Exception
     */
    public function testGetOrganizationConsumerDossierUrl()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        $connect = $this->getConnect($mockGuzzleClient);
        $uuid = Uuid::uuid4();

        $this->assertEquals('baz/consumers/' . $uuid->toString(), $connect->getOrganizationConsumerDossierUrl(
            $uuid
        ));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetConnectionStatus()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $uuid = (new UuidFactory())->fromString(Uuid::uuid4());
        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode([
                    'uwkluis_consumer_id' => $uuid->toString(),
                    'status'          => Status::NEW,
                    'granted_scopes'          => 'foo bar',
                ])
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->assertEquals(new Connection(
            $uuid,
            new Status(Status::NEW),
            ['foo', 'bar']
        ), $connect->getConnectionStatus($token, $uuid));

        $mockGuzzleClient->method('request')
            ->willThrowException(
                new ClientException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response()
                )
            );

        try {
            $connect->getConnectionStatus($token, $uuid);
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }

        $mockGuzzleClient->method('request')
            ->willThrowException(
                new ClientException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response(StatusCodeInterface::STATUS_UNAUTHORIZED)
                )
            );

        try {
            $connect->getConnectionStatus($token, $uuid);
        } catch (Throwable $e) {
            $this->assertInstanceOf(OrganizationConnectionException::class, $e);
            $this->assertEquals('Organization connection failed', $e->getMessage());
        }

        $mockGuzzleClient->method('request')
            ->willThrowException(
                new Exception(
                    'foo'
                )
            );

        try {
            $connect->getConnectionStatus($token, $uuid);
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }
    }

    public function testInviteConsumer(): void
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $uuid = (new UuidFactory())->fromString(Uuid::uuid4());
        $connect = $this->createConnect($uuid);

        $this->assertEquals(new Connection($uuid), $connect->inviteConsumer(
            $token,
            'foo@example.org',
            '0612345678'
        ));
    }

    public function testInviteConsumerWithInvalidEmailAddress(): void
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $uuid = (new UuidFactory())->fromString(Uuid::uuid4());
        $connect = $this->createConnect($uuid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "foo" was expected to be a valid e-mail address.');

        $connect->inviteConsumer(
            $token,
            'foo',
            '0612345678'
        );
    }

    public function testInviteConsumerWithConsumerConnectionConflict(): void
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        /** @var UwkluisClient $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);

        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new ClientException(
                'foo',
                new Request('get', 'foo'),
                new Response(StatusCodeInterface::STATUS_OK)
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->expectException(ConsumerConnectionException::class);
        $this->expectExceptionMessage('Consumer connection failed');

        $connect->inviteConsumer(
            $token,
            'foo@example.org',
            '0612345678'
        );
    }

    public function testInviteConsumerWithOrganizationConnectionConflict(): void
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);

        /** @var UwkluisClient $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        $mockGuzzleClient->method('request')
            ->willThrowException(new ClientException(
                'foo',
                new Request('get', 'foo'),
                new Response(StatusCodeInterface::STATUS_UNAUTHORIZED)
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->expectException(OrganizationConnectionException::class);
        $this->expectExceptionMessage('Organization connection failed');

        $connect->inviteConsumer(
            $token,
            'foo@example.org',
            '0612345678'
        );
    }

    public function testInviteConsumerWithDuplicateEmailAndPhoneNumber(): void
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);

        $uuid = (new UuidFactory())->fromString(Uuid::uuid4());

        /** @var UwkluisClient $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);

        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new ClientException(
                'foo',
                new Request('get', 'foo'),
                new Response(
                    StatusCodeInterface::STATUS_CONFLICT,
                    [],
                    json_encode([
                        "message" => 'Consumer with this email and phone number is already connected or invited',
                        "data" => [
                            "uwkluis_consumer_id" => $uuid->toString(),
                        ],
                    ])
                )
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->expectException(ConsumerConnectionConflict::class);
        $this->expectExceptionMessage('Consumer with this email and phone number is already connected or invited');

        try {
            $connect->inviteConsumer(
                $token,
                'foo@example.org',
                '0612345678'
            );
        } catch (ConsumerConnectionConflict $e) {
            $this->assertEquals(new Connection($uuid), $e->getConflictingConnection());
            // Rethrow so PHPUnit can assert the exception
            throw $e;
        }
    }

    public function testInviteConsumerWithUnknownException(): void
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);

        /** @var UwkluisClient $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);

        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new Exception(
                'foo'
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->expectException(ConsumerConnectionException::class);
        $this->expectExceptionMessage('Consumer connection failed');

        $connect->inviteConsumer(
            $token,
            'foo@example.org',
            '0612345678'
        );
    }

    /**
     * @throws AssertionFailedException
     * @throws GuzzleException
     * @throws Exception
     */
    public function testUpdateAndReinviteConsumer()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $response = new stdClass();
        $uuid = (new UuidFactory())->fromString(Uuid::uuid4()->toString());
        $response->uwkluis_consumer_id = $uuid;

        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode($response)
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->assertEquals(
            new Connection($uuid),
            $connect->updateAndReinviteConsumer(
                $token,
                $uuid,
                'foo@example.org',
                '0612345678'
            )
        );

        $mockGuzzleClient->method('request')
            ->willThrowException(
                new ClientException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response()
                )
            );

        try {
            $connect->updateAndReinviteConsumer(
                $token,
                $uuid,
                'foo@example.org',
                '0612345678'
            );
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }

        $mockGuzzleClient->method('request')
            ->willThrowException(
                new ClientException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response(StatusCodeInterface::STATUS_UNAUTHORIZED)
                )
            );

        try {
            $connect->updateAndReinviteConsumer(
                $token,
                $uuid,
                'foo@example.org',
                '0612345678'
            );
        } catch (Throwable $e) {
            $this->assertInstanceOf(OrganizationConnectionException::class, $e);
            $this->assertEquals('Organization connection failed', $e->getMessage());
        }

        $mockGuzzleClient->method('request')
            ->willThrowException(
                new Exception(
                    'foo'
                )
            );

        try {
            $connect->updateAndReinviteConsumer(
                $token,
                $uuid,
                'foo@example.org',
                '0612345678'
            );
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }
    }

    private function getConnect(UwkluisClientInterface $mockGuzzleClient)
    {
        return new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory()
        );
    }

    private function createConnect(UuidInterface $uuid): Connect
    {
        /** @var UwkluisClient $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(UwkluisClient::class);
        $response = ['uwkluis_consumer_id' => $uuid->toString()];

        $mockGuzzleClient->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode($response)
            ));

        return $this->getConnect($mockGuzzleClient);
    }
}
