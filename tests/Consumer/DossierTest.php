<?php

namespace UwKluis\Client\Consumer;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Throwable;
use UwKluis\Client\Exception\ConsumerConnectionException;
use UwKluis\Client\Exception\InvalidRequestException;
use UwKluis\Client\Exception\OrganizationConnectionException;
use UwKluis\Client\Exception\ValidationException;
use UwKluis\Client\Organization\Config;

class DossierTest extends TestCase
{
    use ChecksResponseFlow;

    /**
     * @throws Exception
     */
    public function testUpdateData()
    {
        $this->checkResponseFlow('updateData', ['1'], 1);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetData()
    {
        $this->checkResponseFlow('getData', 1);
        $uuid = (new UuidFactory())->fromString(Uuid::uuid4());
        $this->checkBadFlow(
            new Response(
                StatusCodeInterface::STATUS_IM_A_TEAPOT,
                [],
                'An unknown error has occurred'
            ),
            $uuid,
            new InvalidRequestException('An unknown error has occurred', StatusCodeInterface::STATUS_IM_A_TEAPOT)
        );
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
     * @param ResponseInterface $response
     * @param UuidInterface $uuid
     * @param Throwable $expectedException
     *
     * @return Exception|Throwable
     * @throws GuzzleException
     */
    private function checkBadFlow(ResponseInterface $response, UuidInterface $uuid, Throwable $expectedException)
    {
        /** @var Token $token */
        $token = $this->createMock(Token::class);
        $mockGuzzleClient = $this->getMockGuzzleClient();
        $mockGuzzleClient->method('request')
            ->willThrowException(new BadResponseException(
                'foo',
                new Request('get', 'foo'),
                $response
            ));
        try {
            $this->getApiClient($mockGuzzleClient)->getData(
                $token,
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

    /**
     * @param ClientInterface $client
     * @return Dossier
     */
    public function getApiClient(ClientInterface $client)
    {
        /** @noinspection PhpParamsInspection */
        return new Dossier(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $client
        );
    }
}
