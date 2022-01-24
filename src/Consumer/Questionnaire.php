<?php
declare(strict_types=1);

namespace UwKluis\Client\Consumer;

use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Token;
use Psr\Http\Message\ResponseInterface;
use UwKluis\Client\Organization\Config;
use UwKluis\Client\Traits\ProcessesBadResponses;

/**
 * Class Questionnaire
 */
final class Questionnaire
{
    use ProcessesBadResponses;

    /**
     * @var Config
     */
    private $config;
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * Message constructor.
     * @param Config $config
     * @param ClientInterface $guzzleClient
     */
    public function __construct(
        Config $config,
        ClientInterface $guzzleClient
    )
    {
        $this->config = $config;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Lists available questionnaires to start.
     *
     * @param Token $accessToken
     * @return array
     * @throws GuzzleException
     */
    public function listAvailable(Token $accessToken): array
    {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/questionnaires/available",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
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

    /**
     * Lists started questionnaires.
     *
     * @param Token $accessToken
     * @param string $consumerId
     *
     * @return array
     * @throws GuzzleException
     */
    public function list(
        Token $accessToken,
        string $consumerId
    ): array
    {
        $queryString = http_build_query([
            'consumer_id' => $consumerId,
        ]);
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/questionnaires?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
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

    /**
     * @param Token $token
     * @param string $consumerId
     * @param string $questionnaireId
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function get(
        Token $accessToken,
        string $consumerId,
        string $questionnaireId
    ): ResponseInterface
    {
        $queryString = http_build_query(['consumer_id' => $consumerId]);
        try {
            $response = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_GET,
                "{$this->config->getApiHost()}/questionnaires/{$questionnaireId}?{$queryString}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                ]
            );
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        return $response;
    }

    /**
     * @param Token $token
     * @param string $consumerId
     * @param array $data
     *
     * @return array
     * @throws GuzzleException
     */
    public function start(
        Token $accessToken,
        string $consumerId,
        array $data
    ): array
    {
        try {
            $httpResponse = $this->guzzleClient->request(
                RequestMethodInterface::METHOD_POST,
                "{$this->config->getApiHost()}/questionnaires/consumer/{$consumerId}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken->toString(),
                    ],
                    RequestOptions::FORM_PARAMS => $data,
                ]
            )->getBody()->getContents();
        } catch (BadResponseException $e) {
            $this->processBadResponse($e);
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return json_decode($httpResponse, true);
    }
}
