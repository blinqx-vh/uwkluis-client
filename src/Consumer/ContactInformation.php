<?php
declare(strict_types = 1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

final class ContactInformation
{
    use ProcessesBadResponses;

    private ClientInterface $guzzleClient;
    private Config $config;

    public function __construct(
        ClientInterface $guzzleClient,
        Config $config
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config       = $config;
    }

    public function post(Token $accessToken, string $consumerId, array $contactData): void
    {
        try {
            $httpResponse =
                $this->guzzleClient->request(
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
                )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }
    }
}