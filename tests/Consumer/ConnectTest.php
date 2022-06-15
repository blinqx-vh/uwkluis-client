<?php

namespace UwKluis\Client\Consumer;

use Assert\InvalidArgumentException;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use UwKluis\Client\Exception\ConsumerConnectionConflict;
use UwKluis\Client\Exception\ConsumerConnectionException;
use UwKluis\Client\Exception\InvalidPhoneNumberException;
use UwKluis\Client\Exception\OrganizationConnectionException;
use UwKluis\Client\Helpers\Sms;
use UwKluis\Client\Organization\Config;
use UwKluis\Enums\ConsumerConnection\Status;

class ConnectTest extends TestCase
{

    public function testGetOrganizationConsumersUrl()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(Client::class);
        $connect = $this->getConnect($mockGuzzleClient);

        $this->assertEquals('baz/consumers/', $connect->getOrganizationConsumersUrl());
    }

    /**
     * @throws \Exception
     */
    public function testGetOrganizationConsumerDossierUrl()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(Client::class);
        $connect = $this->getConnect($mockGuzzleClient);
        $uuid = Uuid::uuid4();

        $this->assertEquals('baz/consumers/' . $uuid->toString(), $connect->getOrganizationConsumerDossierUrl(
            $uuid
        ));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testGetConnectionStatus()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(Client::class);
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }
    }

    /**
     * @throws \Assert\AssertionFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function testInviteConsumer()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(Client::class);
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $response = new \stdClass();
        $uuid = (new UuidFactory())->fromString(Uuid::uuid4());
        $response->uwkluis_consumer_id = $uuid;

        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode($response)
            ));

        $connect = $this->getConnect($mockGuzzleClient);

        $this->assertEquals(new Connection($uuid), $connect->inviteConsumer(
            $token,
            'foo@example.org',
            '0612345678'
        ));

        try {
            $connect->inviteConsumer(
                $token,
                'foo',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertEquals('Value "foo" was expected to be a valid e-mail address.', $e->getMessage());
        }

        try {
            $connect->inviteConsumer(
                $token,
                'foo@example.org',
                '0612345'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidPhoneNumberException::class, $e);
            $this->assertEquals('Invalid phone number: 0612345', $e->getMessage());
        }

        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new ClientException(
                'foo',
                new Request('get', 'foo'),
                new Response(StatusCodeInterface::STATUS_OK)
            ));

        try {
            $connect->inviteConsumer(
                $token,
                'foo@example.org',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }

        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new ClientException(
                'foo',
                new Request('get', 'foo'),
                new Response(StatusCodeInterface::STATUS_UNAUTHORIZED)
            ));

        try {
            $connect->inviteConsumer(
                $token,
                'foo@example.org',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(OrganizationConnectionException::class, $e);
            $this->assertEquals('Organization connection failed', $e->getMessage());
        }

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
                        "data"    => [
                            "uwkluis_consumer_id" => $uuid->toString(),
                        ],
                    ])
                )
            ));

        try {
            $connect->inviteConsumer(
                $token,
                'foo@example.org',
                '0612345678'
            );
        } catch (\Throwable $e) {
            /** @var $e ConsumerConnectionConflict */
            $this->assertInstanceOf(ConsumerConnectionConflict::class, $e);
            $this->assertEquals(
                'Consumer with this email and phone number is already connected or invited',
                $e->getMessage()
            );
            $this->assertEquals(new Connection($uuid), $e->getConflictingConnection());
        }

        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new Exception(
                'foo'
            ));

        try {
            $connect->inviteConsumer(
                $token,
                'foo@example.org',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }
    }

    /**
     * @throws \Assert\AssertionFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function testUpdateAndReinviteConsumer()
    {
        /** @var Client $mockGuzzleClient */
        $mockGuzzleClient = $this->createMock(Client::class);
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $response = new \stdClass();
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }
    }

    private function getConnect(Client $mockGuzzleClient): Connect
    {
        return new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory(),
            new Sms()
        );
    }
}
