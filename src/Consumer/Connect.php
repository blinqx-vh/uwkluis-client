<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Assert\Assertion;
use Exception;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use UwKluis\Client\Exception\ConsumerConnectionConflict;
use UwKluis\Client\Exception\ConsumerConnectionException;
use UwKluis\Client\Exception\OrganizationConnectionException;
use UwKluis\Client\Organization\Config;
use UwKluis\Enums\ConsumerConnection\Status;

/**
 * Class Connect
 */
final class Connect
{
    /**
     * Phone number validation regex
     */
    const PHONE_NUMBER_REGEX = '/^((((00|\+)31|0)6){1}[1-9]{1}[0-9]{7})$/';

    /** @var ClientInterface */
    private $guzzleClient;
    /** @var Config */
    private $config;
    /** @var UuidFactoryInterface */
    private $uuidFactory;

    /**
     * Connect constructor.
     *
     * @param Config               $config
     * @param ClientInterface      $guzzleClient
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient,
        UuidFactoryInterface $uuidFactory
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param Token  $accessToken
     * @param string $email
     * @param string $phoneNumber
     *
     * @return Connection
     * @throws \Assert\AssertionFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function inviteConsumer(Token $accessToken, string $email, string $phoneNumber): Connection
    {
        Assertion::email($email);
        Assertion::regex($phoneNumber, self::PHONE_NUMBER_REGEX);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                $this->config->getApiHost() . '/consumer/invite',
                [
                    RequestOptions::FORM_PARAMS => [
                        'email'        => $email,
                        'phone_number' => $phoneNumber,
                    ],
                    RequestOptions::HEADERS     => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === StatusCodeInterface::STATUS_UNAUTHORIZED) {
                throw new OrganizationConnectionException('Organization connection failed', $e->getCode(), $e);
            } elseif ($e->getResponse()->getStatusCode() === StatusCodeInterface::STATUS_CONFLICT) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                throw new ConsumerConnectionConflict(
                    $response->message,
                    $e->getCode(),
                    $e,
                    new Connection($this->uuidFactory->fromString($response->data->uwkluis_consumer_id))
                );
            }
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        }

        return new Connection(
            $this->uuidFactory->fromString(json_decode($httpResponse->getBody()->getContents())->uwkluis_consumer_id)
        );
    }

    /**
     * @param Token         $accessToken
     * @param UuidInterface $identifier
     * @param string        $email
     * @param string        $phoneNumber
     *
     * @return Connection
     * @throws \Assert\AssertionFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateAndReinviteConsumer(
        Token $accessToken,
        UuidInterface $identifier,
        string $email,
        string $phoneNumber
    ): Connection {
        Assertion::email($email);
        Assertion::regex($phoneNumber, self::PHONE_NUMBER_REGEX);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                $this->config->getApiHost() . '/consumer/update-and-reinvite',
                [
                    RequestOptions::FORM_PARAMS => [
                        'email'               => $email,
                        'phone_number'        => $phoneNumber,
                        'consumer_identifier' => $identifier->toString(),
                    ],
                    RequestOptions::HEADERS     => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === StatusCodeInterface::STATUS_UNAUTHORIZED) {
                throw new OrganizationConnectionException('Organization connection failed', $e->getCode(), $e);
            }
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        }

        return new Connection(
            $this->uuidFactory->fromString(json_decode($httpResponse->getBody()->getContents())->uwkluis_consumer_id)
        );
    }

    /**
     * @param Token         $accessToken
     * @param UuidInterface $identifier
     *
     * @return Connection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getConnectionStatus(
        Token $accessToken,
        UuidInterface $identifier
    ): Connection {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                $this->config->getApiHost() . '/consumer/get-connection-status?' . http_build_query(
                    [
                        'consumer_identifier' => $identifier->toString(),
                    ]
                ),
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . (string) $accessToken,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === StatusCodeInterface::STATUS_UNAUTHORIZED) {
                throw new OrganizationConnectionException('Organization connection failed', $e->getCode(), $e);
            }
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        }

        $connection = json_decode($httpResponse->getBody()->getContents());

        return new Connection(
            $this->uuidFactory->fromString($connection->uwkluis_consumer_id),
            new Status($connection->status),
            $connection->granted_scopes ? explode(' ', $connection->granted_scopes) : null
        );
    }

    /**
     * @param UuidInterface $uuid
     *
     * @return string
     */
    public function getOrganizationConsumerDossierUrl(UuidInterface $uuid): string
    {
        return $this->config->getOrganizationHost() . '/consumers/' . $uuid->toString();
    }

    /**
     * @return string
     */
    public function getOrganizationConsumersUrl(): string
    {
        return $this->config->getOrganizationHost() . '/consumers/';
    }

    /**
     * @param Token $accessToken
     *
     * @param UuidInterface $identifier
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function disconnect(
        Token $accessToken,
        UuidInterface $identifier
    ): void
    {
        try {
            $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                $this->config->getApiHost() . '/consumer/disconnect?' . http_build_query(
                    [
                        'consumer_id' => $identifier->toString(),
                    ]
                ),
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]
            );
        } catch (Exception $e) {
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        }
    }
}
