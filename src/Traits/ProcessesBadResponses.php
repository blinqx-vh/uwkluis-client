<?php
declare(strict_types = 1);

namespace UwKluis\Client\Traits;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Exception\BadResponseException;
use UwKluis\Client\Exception\ConsumerConnectionException;
use UwKluis\Client\Exception\InvalidRequestException;
use UwKluis\Client\Exception\OrganizationConnectionException;
use UwKluis\Client\Exception\ValidationException;

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
                $errors = json_decode($exceptionResponse['message'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors = [$exceptionResponse['message']];
                }
                throw (new ValidationException('Validation failed', $e->getCode(), $e))
                    ->setValidationErrors($errors);
            }
        }

        throw new InvalidRequestException(
            $contents ?? 'An unknown error has occurred',
            $e->getResponse() ? $e->getResponse()->getStatusCode() : 0,
            $e
        );
    }
}
