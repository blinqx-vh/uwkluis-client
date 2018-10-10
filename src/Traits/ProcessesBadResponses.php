<?php
declare(strict_types = 1);

namespace Ufo\Client\Traits;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Exception\BadResponseException;
use Ufo\Client\Exception\ConsumerConnectionException;
use Ufo\Client\Exception\InvalidRequestException;
use Ufo\Client\Exception\OrganizationConnectionException;
use Ufo\Client\Exception\ValidationException;

/**
 * Class ProcessesBadResponses
 */
trait ProcessesBadResponses
{
    /**
     * @param BadResponseException $e
     */
    private function processBadResponse(BadResponseException $e)
    {
        if ($e->getResponse()) {
            $contents = $e->getResponse()->getBody()->getContents();
            $exceptionResponse = json_decode($contents, true);
            if (($e->getCode() === StatusCodeInterface::STATUS_FORBIDDEN
                    && $exceptionResponse === 'Invalid consumer connection'
                )
                || ($e->getCode() === StatusCodeInterface::STATUS_NOT_FOUND
                    && $exceptionResponse === 'consumer connection not found'
                )
            ) {
                throw new ConsumerConnectionException($exceptionResponse, $e->getCode(), $e);
            }

            if ($e->getCode() === StatusCodeInterface::STATUS_UNAUTHORIZED) {
                throw new OrganizationConnectionException(
                    'Invalid organization connection',
                    StatusCodeInterface::STATUS_FORBIDDEN,
                    $e
                );
            }

            if ($e->getCode() === StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY) {
                throw  (new ValidationException('Validation failed', $e->getCode(), $e))
                    ->setValidationErrors(json_decode($exceptionResponse['message']));
            }
        }

        throw new InvalidRequestException(
            $e->getResponse() ? $contents : 'An unknown error has occurred',
            $e->getResponse() ? $e->getResponse()->getStatusCode() : 0,
            $e
        );
    }
}
