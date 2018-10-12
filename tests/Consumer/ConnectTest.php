<?php

namespace Ufo\Client\Consumer;

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
use Ufo\Client\Exception\ConsumerConnectionConflict;
use Ufo\Client\Exception\ConsumerConnectionException;
use Ufo\Client\Exception\OrganizationConnectionException;
use Ufo\Client\Organization\Config;
use UwKluis\Enums\ConsumerConnection\Status;

class ConnectTest extends TestCase
{

    /**
     *
     */
    public function testGetOrganizationConsumersUrl()
    {
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        /** @noinspection PhpParamsInspection */
        $connect = new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory()
        );
        $this->assertEquals('baz/consumers/', $connect->getOrganizationConsumersUrl());
    }

    /**
     * @throws \Exception
     */
    public function testGetOrganizationConsumerDossierUrl()
    {
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        /** @noinspection PhpParamsInspection */
        $connect = new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory()
        );
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
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $uuid = Uuid::uuid4();
        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode([
                    'ufo_consumer_id' => $uuid->toString(),
                    'status'          => Status::NEW,
                    'scopes'          => ['foo'],
                ])
            ));

        /** @noinspection PhpParamsInspection */
        $connect = new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory()
        );
        $this->assertEquals(new Connection(
            $uuid,
            new Status(Status::NEW),
            ['foo']
        ), $connect->getConnectionStatus(new Token(), $uuid));
        $mockGuzzleClient->method('request')
            ->willThrowException(
                new ClientException(
                    'foo',
                    new Request('get', 'foo'),
                    new Response()
                )
            );
        try {
            $connect->getConnectionStatus(new Token(), $uuid);
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
            $connect->getConnectionStatus(new Token(), $uuid);
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
            $connect->getConnectionStatus(new Token(), $uuid);
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
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $response = new \stdClass();
        $uuid = Uuid::uuid4();
        $response->ufo_consumer_id = $uuid;
        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode($response)
            ));

        /** @noinspection PhpParamsInspection */
        $connect = new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory()
        );


        $this->assertEquals(new Connection($uuid), $connect->inviteConsumer(
            new Token(),
            'foo@example.org',
            '0612345678'
        ));

        try {
            $connect->inviteConsumer(
                new Token(),
                'foo',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertEquals('Value "foo" was expected to be a valid e-mail address.', $e->getMessage());
        }
        try {
            $connect->inviteConsumer(
                new Token(),
                'foo@example.org',
                '0612345'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertEquals('Value "0612345" does not match expression.', $e->getMessage());
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
                new Token(),
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
                new Token(),
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
                new Response(StatusCodeInterface::STATUS_CONFLICT, [],
                    json_encode([
                        "message" => 'Consumer with this email and phone number is already connected or invited',
                        "data"    => [
                            "ufo_consumer_id" => $uuid->toString(),
                        ],
                    ]))
            ));
        try {
            $connect->inviteConsumer(
                new Token(),
                'foo@example.org',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionConflict::class, $e);
            $this->assertEquals(
                'Consumer with this email and phone number is already connected or invited',
                $e->getMessage()
            );
        }
        $mockGuzzleClient
            ->method('request')
            ->willThrowException(new Exception(
                'foo'
            ));
        try {
            $connect->inviteConsumer(
                new Token(),
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
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $response = new \stdClass();
        $uuid = Uuid::uuid4();
        $response->ufo_consumer_id = $uuid;
        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode($response)
            ));

        /** @noinspection PhpParamsInspection */
        $connect = new Connect(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient,
            new UuidFactory()
        );


        $this->assertEquals(
            new Connection($uuid),
            $connect->updateAndReinviteConsumer(
                new Token(),
                $uuid,
                'foo@example.org',
                '0612345678'
            ));

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
                new Token(),
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
                new Token(),
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
                new Token(),
                $uuid,
                'foo@example.org',
                '0612345678'
            );
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ConsumerConnectionException::class, $e);
            $this->assertEquals('Consumer connection failed', $e->getMessage());
        }
    }
}
