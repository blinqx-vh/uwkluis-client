<?php
declare(strict_types=1);

namespace Ufo\Client\Consumer;

use Assert\Assertion;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use Ufo\Client\Exception\ConsumerRequestException;
use Ufo\Client\Organization\Config;

/**
 * Class Connect
 */
final class Connect
{
    /** @var GuzzleClient */
    private $guzzleClient;
    /** @var Config */
    private $config;
    /** @var UuidFactoryInterface */
    private $uuidFactory;

    /**
     * Connection constructor.
     *
     * @param Config $config
     * @param GuzzleClient $guzzleClient
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        Config $config,
        GuzzleClient $guzzleClient,
        UuidFactoryInterface $uuidFactory
    )
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param Token $accessToken
     * @param string $email
     * @param string $phoneNumber
     *
     * @return UuidInterface - the Uuid for the consumer
     * @throws \Assert\AssertionFailedException
     */
    public function inviteConsumer(Token $accessToken, string $email, string $phoneNumber): UuidInterface
    {
        Assertion::email($email);
        Assertion::regex($phoneNumber, '/^((((00|\+)31|0)6){1}[1-9]{1}[0-9]{7})$/');

        try {
            $httpResponse = $this->guzzleClient->post(
                $this->config->getApiHost() . '/consumer/invite',
                [
                    RequestOptions::FORM_PARAMS => [
                        'email' => $email,
                        'phone_number' => $phoneNumber,
                    ],
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$accessToken,
                    ],
                ]
            );
        } catch (\Exception $e) {
            throw new ConsumerRequestException('Consumer connection failed', $e->getCode(), $e);
        }
        return $this->uuidFactory->fromString(json_decode($httpResponse->getBody()->getContents())->ufo_consumer_id);
    }

    /**
     * @param Token $accessToken
     * @param string $email
     * @param string $phoneNumber
     *
     * @return UuidInterface
     */
    public function reinviteConsumer(Token $accessToken, string $email, string $phoneNumber): UuidInterface
    {
        Assertion::email($email);
        Assertion::regex($phoneNumber, '/^((((00|\+)31|0)6){1}[1-9]{1}[0-9]{7})$/');

        try {
            $httpResponse = $this->guzzleClient->post(
                $this->config->getApiHost() . '/consumer/reinvite',
                [
                    RequestOptions::FORM_PARAMS => [
                        'email' => $email,
                        'phone_number' => $phoneNumber,
                    ],
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . (string)$accessToken,
                    ],
                ]
            );
        } catch (\Exception $e) {
            throw new ConsumerRequestException('Consumer connection failed', $e->getCode(), $e);
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
