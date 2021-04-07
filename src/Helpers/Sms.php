<?php
declare(strict_types=1);

namespace UwKluis\Client\Helpers;

use UwKluis\Client\Exception\InvalidPhoneNumberException;

final class Sms
{
    /**
     * @param string $number
     * @param string $prefix
     *
     * @return string
     * @throws InvalidPhoneNumberException
     */
    public function sanitizeAndInternationalizePhoneNumber(string $number, string $prefix = ''): string
    {
        $ukRegex = '/(^(447){1}[1-9]{1}[0-9]{8})/';
        $nlRegex = '/(^(316){1}[1-9]{1}[0-9]{7})/';
        $beRegex = '/(^(324){1}[1-9]{1}[0-9]{7})/';
        $deRegex = '/(^(491){1}[1-9]{1}[0-9]{9})/';

        $originalInput = $number;
        $number = preg_replace([
            '/\D+/', // Filter all non numeric characters
            '/^00/', // Replace 00 with empty value
            '/^06/', // Replace local dutch (06) prefix with international dutch (316) prefix
        ], ['','', '316'], $number);

        $number = preg_replace(
            '/^07/', // Replace local UK (07) prefix with international UK (447) prefix
            '447',
            $number
        );

        $number = preg_replace(
            '/^04/', // Replace local BE (04) prefix with international BE (324) prefix
            '324',
            $number
        );

        $number = preg_replace(
            '/^01/', // Replace local DE (01) prefix with international DE (491) prefix
            '491',
            $number
        );

        // Validate
        if (preg_match($nlRegex, $number) === 0 &&
            preg_match($ukRegex, $number) === 0 &&
            preg_match($beRegex, $number) === 0 &&
            preg_match($deRegex, $number) === 0
        ) {
            throw new InvalidPhoneNumberException('Invalid phone number: ' . $originalInput);
        }

        return $prefix . $number;
    }
}