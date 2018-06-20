<?php
declare(strict_types = 1);

namespace Ufo\Client\Consumer;

use Assert\Assertion;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use Ufo\Client\Exception\ConsumerConnectionConflict;
use Ufo\Client\Exception\ConsumerConnectionException;
use Ufo\Client\Exception\OrganizationConnectionException;
use Ufo\Client\Organization\Config;

/**
 * Class Connect
 */
final class Connect
{
    /**
     * Phone number validation regex
     */
    const PHONE_NUMBER_REGEX = '/^((((00|\+)31|0)6){1}[1-9]{1}[0-9]{7})$/';

    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;
    /** @var UuidFactoryInterface */
    private $uuidFactory;

    /**
     * Connection constructor.
     *
     * @param Config               $config
     * @param GuzzleClient         $guzzleClient
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        Config $config,
        GuzzleClient $guzzleClient,
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
     * @return UuidInterface - the Uuid for the consumer
     * @throws \Assert\AssertionFailedException
     */
    public function inviteConsumer(Token $accessToken, string $email, string $phoneNumber): UuidInterface
    {
        Assertion::email($email);
        Assertion::regex($phoneNumber, self::PHONE_NUMBER_REGEX);

        try {
            $httpResponse = $this->guzzleClient->post(
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
                throw new ConsumerConnectionConflict(
                    'Consumer with this email and phone number is already connected or invited',
                    $e->getCode(),
                    $e
                );
            }
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new ConsumerConnectionException('Consumer connection failed', $e->getCode(), $e);
        }

        return $this->uuidFactory->fromString(json_decode($httpResponse->getBody()->getContents())->ufo_consumer_id);
    }

    /**
     * @param Token         $accessToken
     * @param UuidInterface $identifier
     * @param string        $email
     * @param string        $phoneNumber
     *
     * @return UuidInterface
     * @throws \Assert\AssertionFailedException
     */
    public function updateAndReinviteConsumer(
        Token $accessToken,
        UuidInterface $identifier,
        string $email,
        string $phoneNumber
    ): UuidInterface {
        Assertion::email($email);
        Assertion::regex($phoneNumber, self::PHONE_NUMBER_REGEX);

        try {
            $httpResponse = $this->guzzleClient->post(
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

        return $this->uuidFactory->fromString(json_decode($httpResponse->getBody()->getContents())->ufo_consumer_id);
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
}
