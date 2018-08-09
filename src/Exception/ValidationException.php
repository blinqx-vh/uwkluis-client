<?php
declare(strict_types = 1);

namespace Ufo\Client\Exception;

/**
 * Class OrganizationConnectionException
 */
class ValidationException extends \RuntimeException
{
    /** @var array */
    private $validationErrors =  [];

    /**
     * @param array $validationErrors
     *
     * @return ValidationException
     */
    public function setValidationErrors(array $validationErrors): ValidationException
    {
        $this->validationErrors = $validationErrors;

        return $this;
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
