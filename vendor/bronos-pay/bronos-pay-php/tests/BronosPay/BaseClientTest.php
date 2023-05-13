<?php

namespace BronosPay;

/**
 * @internal
 * @covers \BronosPay\BaseClient
 */
class BaseClientTest extends TestCase
{
    public function testCtorThrowsIfApiKeyIsUnexpectedType()
    {
        $this->expectException(\BronosPay\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('api_key must be null or a string');

        $client = new BaseClient(234);
    }

    public function testCtorThrowsIfApiKeyIsEmpty()
    {
        $this->expectException(\BronosPay\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('api_key cannot be empty string');

        $client = new BaseClient('');
    }

    public function testCtorThrowsIfApiKeyContainsWhitespace()
    {
        $this->expectException(\BronosPay\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('api_key cannot contain whitespace');

        $client = new BaseClient(self::APIKEY . "\n");
    }

    public function testRequestThrowsIfInvalidApiKeyUsed()
    {
        $this->expectException(\BronosPay\Exception\Api\Unauthorized::class);

        $client = new BaseClient('123456789', true);

        $client->request('get', '/api/v1/orders/get?id=1');
    }

    public function testPublicRequestWithEmptyApiKey()
    {
        $client = new BaseClient();

        $response = $client->request('get', '/api/v1/ping');

        $this->assertNotEmpty($response);
    }

    public function testConnectionWithApiKey()
    {
        $client = new BaseClient();

        $response = $client->testConnection(self::APIKEY, true);

        $this->assertNotEmpty($response);
        $this->assertSame($response, true);
    }

    public function testApiKeyAssignment()
    {
        $client = new BaseClient();

        $client->setApiKey(self::APIKEY);

        $this->assertEquals($client->getApiKey(), self::APIKEY);
    }
}
