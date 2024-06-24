<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Client\UwkluisClientInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

final class ContactInformation
{
    use ProcessesBadResponses;

    public function __construct(
        private readonly UwkluisClientInterface $uwkluisClient,
        private readonly Config                 $config
    ) {
    }

    public function post(Token $accessToken, string $consumerId, array $contactData): void
    {
        try {
            $this->uwkluisClient->request(
                    RequestMethodInterface::METHOD_POST,
                    $this->config->getApiHost() . '/contact-information',
                    [
                        RequestOptions::HEADERS     => [
                            'Accept'        => 'application/json',
                            'Authorization' => 'Bearer ' . $accessToken->toString(),
                        ],
                        RequestOptions::QUERY => [
                            'consumer_id' => $consumerId,
                        ],
                        RequestOptions::FORM_PARAMS => [
                            'body' => json_encode($contactData),
                        ],
                    ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }
    }
}