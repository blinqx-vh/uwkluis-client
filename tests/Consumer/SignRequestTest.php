<?php
declare(strict_types=1);

namespace UwKluis\Client\Consumer;

use Exception;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use UwKluis\Client\Organization\Config;

class SignRequestTest extends TestCase
{
    use ChecksResponseFlow;

    /**
     * @throws Exception
     */
    public function testList()
    {
        $this->checkResponseFlow('list');
    }

    public function getApiClient(ClientInterface $client)
    {
        /** @noinspection PhpParamsInspection */
        return new SignRequest(
            (new Config(
                'foo',
                'bar'
            ))->setOrganizationHost('baz'),
            $client
        );
    }
}
