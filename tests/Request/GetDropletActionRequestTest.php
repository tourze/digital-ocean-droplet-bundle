<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\GetDropletActionRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(GetDropletActionRequest::class)]
final class GetDropletActionRequestTest extends RequestTestCase
{
    private const DROPLET_ID = 12345;
    private const ACTION_ID = 67890;

    private GetDropletActionRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new GetDropletActionRequest(self::DROPLET_ID, self::ACTION_ID);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetDropletActionRequest::class, $this->request);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/droplets/' . self::DROPLET_ID . '/actions/' . self::ACTION_ID, $this->request->getRequestPath());
    }

    public function testSetApiKey(): void
    {
        $apiKey = 'test-api-key';
        $this->request->setApiKey($apiKey);

        $this->assertEquals($apiKey, $this->request->getApiKey());
    }
}
