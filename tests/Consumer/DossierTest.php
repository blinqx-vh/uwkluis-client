<?php

namespace Ufo\Client\Consumer;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;
use Ufo\Client\Exception\ConsumerConnectionException;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Exception\OrganizationConnectionException;
use Ufo\Client\Exception\ValidationException;
use Ufo\Client\Organization\Config;

class DossierTest extends TestCase
{
    use WithMockGuzzleClient;

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUpdateData()
    {
        $uuid = Uuid::uuid4();
        $this->assertEquals(['foo'], $this->getDossier($this->getMockGuzzleClient())->updateData(
            new Token(),
            $uuid->toString(),
            ['1'],
            1
        ));

        $mockGuzzleClient = $this->getMockGuzzleClient();
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                null
            ));
        try {
            $this->getDossier($mockGuzzleClient)->updateData(
                new Token(),
                $uuid->toString(),
                ['1'],
                1
            );
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidRequestException::class, $e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function testGetData()
    {
        $uuid = Uuid::uuid4();

        $this->assertEquals(['foo'], $this->getDossier($this->getMockGuzzleClient())->getData(
            new Token(),
            $uuid->toString(),
            '1'
        ));


        $this->checkBadFlow(null, $uuid, new InvalidRequestException('An unknown error has occurred'));

        $this->checkBadFlow(
            new Response(
                StatusCodeInterface::STATUS_FORBIDDEN,
                [],
                json_encode('Invalid consumer connection')
            ),
            $uuid,
            new ConsumerConnectionException(
                'Invalid consumer connection',
                StatusCodeInterface::STATUS_FORBIDDEN
            )
        );
        $this->checkBadFlow(
            new Response(
                StatusCodeInterface::STATUS_IM_A_TEAPOT,
                [],
                'bar'
            ),
            $uuid,
            new InvalidRequestException(
                'bar',
                StatusCodeInterface::STATUS_IM_A_TEAPOT
            )
        );
        $this->checkBadFlow(
            new Response(
                StatusCodeInterface::STATUS_UNAUTHORIZED,
                [],
                'bar'
            ),
            $uuid,
            new OrganizationConnectionException(
                'Invalid organization connection',
                StatusCodeInterface::STATUS_FORBIDDEN
            )
        );
        $e = $this->checkBadFlow(new Response(
            StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
            [],
            json_encode(['message' => json_encode(['foo'])])
        ), $uuid, new ValidationException(
            'Validation failed',
            StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
        ));
        /** @var ValidationException $e */
        $this->assertEquals(['foo'], $e->getValidationErrors());
    }

    /**
     * @param $mockGuzzleClient
     *
     * @return Dossier
     */
    private function getDossier(MockObject $mockGuzzleClient): Dossier
    {
        /** @noinspection PhpParamsInspection */
        return new Dossier(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $mockGuzzleClient
        );
    }

    /**
     * @param ResponseInterface $response
     * @param UuidInterface     $uuid
     * @param Throwable         $expectedException
     *
     * @return \Exception|Throwable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function checkBadFlow($response, UuidInterface $uuid, Throwable $expectedException)
    {
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                $response
            ));
        try {
            $this->getDossier($mockGuzzleClient)->getData(
                new Token(),
                $uuid->toString(),
                '1'
            );
        } catch (Throwable $e) {
            $this->assertInstanceOf(get_class($expectedException), $e);
            $this->assertEquals($expectedException->getMessage(), $e->getMessage());
            $this->assertEquals($expectedException->getCode(), $e->getCode());
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return $e;
    }
}
