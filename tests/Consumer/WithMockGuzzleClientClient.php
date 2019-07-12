<?php
declare(strict_types=1);


namespace Ufo\Client\Consumer;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;

trait WithMockGuzzleClient
{
    /**
     * @return MockObject
     */
    private function getMockGuzzleClient(): MockObject
    {
        $mockGuzzleClient = $this->getMockBuilder(Client::class)->getMock();
        $mockGuzzleClient->expects($this->any())
            ->method('request')
            ->willReturn(new Response(
                200,
                [],
                json_encode(['foo'])
            ));

        return $mockGuzzleClient;
    }
}