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

class SignRequest
{
    use ProcessesBadResponses;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var Config */
    private $config;

    /**
     * Files constructor.
     *
     * @param Config          $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
    }

    public function list(
        Token $accessToken,
        string $consumerId
    ): array {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/sign-request?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }

    public function delete(
        Token $accessToken,
        string $consumerId,
        int $signRequestId
    ): bool {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);

        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_DELETE,
                "{$this->config->getApiHost()}/sign-request/{$signRequestId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return $httpResponse->getStatusCode() === 200;
    }
}
