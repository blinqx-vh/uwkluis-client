<?php
declare(strict_types = 1);

namespace UwKluis\Client\Helpers;

use PHPUnit\Framework\TestCase;
use UwKluis\Client\Exception\InvalidPhoneNumberException;

class PhoneNumberTest extends TestCase
{
    /**
     * @var Sms
     */
    private $smsHelper;

    /**
     * PhoneNumberTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->smsHelper = new Sms();
    }

    public function test_it_should_build_number_based_on_06_number()
    {
        $testNumber = '0619239142';
        $expected   = '31619239142';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_07_number()
    {
        $testNumber = '07700900796';
        $expected   = '447700900796';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_04_number()
    {
        $testNumber = '0460225254';
        $expected   = '32460225254';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_01_number()
    {
        $testNumber = '015781080674';
        $expected   = '4915781080674';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_return_number_based_on_06_number_with_prefix()
    {
        $testNumber = '0619239142';
        $expected   = '+31619239142';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber, '+')
        );
    }

    public function test_it_should_return_number_based_on_07_number_with_prefix()
    {
        $testNumber = '07700900796';
        $expected   = '+447700900796';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber, '+')
        );
    }

    public function test_it_should_return_number_based_on_04_number_with_prefix()
    {
        $testNumber = '0460225254';
        $expected   = '+32460225254';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber, '+')
        );
    }

    public function test_it_should_return_number_based_on_01_number_with_prefix()
    {
        $testNumber = '015781080674';
        $expected   = '+4915781080674';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber, '+')
        );
    }

    public function test_it_should_build_number_based_on_0031_number()
    {
        $testNumber = '0031618443229';
        $expected   = '31618443229';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_0044_number()
    {
        $testNumber = '00447700900796';
        $expected   = '447700900796';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_0032_number()
    {
        $testNumber = '0032460225254';
        $expected   = '32460225254';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_0049_number()
    {
        $testNumber = '004915781080674';
        $expected   = '4915781080674';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_plus31_number()
    {
        $testNumber = '+31618443229';
        $expected   = '31618443229';
        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_plus44_number()
    {
        $testNumber = '+447700900796';
        $expected   = '447700900796';
        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_plus32_number()
    {
        $testNumber = '+32460225254';
        $expected   = '32460225254';
        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_build_number_based_on_plus49_number()
    {
        $testNumber = '+4915781080674';
        $expected   = '4915781080674';
        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }

    public function test_it_should_throw_exception_when_phone_regex_not_matches()
    {
        $this->expectException(InvalidPhoneNumberException::class);

        $testNumber = '000031618443229';
        $expected   = '31618443229';

        $this->assertEquals(
            $expected,
            $this->smsHelper->sanitizeAndInternationalizePhoneNumber($testNumber)
        );
    }
}