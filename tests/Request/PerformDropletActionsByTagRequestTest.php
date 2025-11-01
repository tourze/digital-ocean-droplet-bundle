<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Enum\DropletActionType;
use DigitalOceanDropletBundle\Request\PerformDropletActionsByTagRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversClass(PerformDropletActionsByTagRequest::class)]
final class PerformDropletActionsByTagRequestTest extends RequestTestCase
{
    private const TAG_NAME = 'web-servers';

    protected function setUp(): void
    {
        parent::setUp();
        // 测试设置
    }

    public function testGetRequestPath(): void
    {
        $request = new PerformDropletActionsByTagRequest(self::TAG_NAME, DropletActionType::REBOOT);
        $this->assertEquals('/droplets/actions?tag_name=' . urlencode(self::TAG_NAME), $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new PerformDropletActionsByTagRequest(self::TAG_NAME, DropletActionType::REBOOT);
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new PerformDropletActionsByTagRequest(self::TAG_NAME, DropletActionType::REBOOT);
        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertEquals('reboot', $options['json']['type']);
    }

    public function testConstructorWithStringActionType(): void
    {
        $request = new PerformDropletActionsByTagRequest(self::TAG_NAME, 'reboot');
        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);

        $this->assertEquals('reboot', $options['json']['type']);
    }

    #[DataProvider('factoryMethodsProvider')]
    public function testFactoryMethods(string $factoryMethod, string $expectedType): void
    {
        $request = match ($factoryMethod) {
            'reboot' => PerformDropletActionsByTagRequest::reboot(self::TAG_NAME),
            'powerOff' => PerformDropletActionsByTagRequest::powerOff(self::TAG_NAME),
            'powerOn' => PerformDropletActionsByTagRequest::powerOn(self::TAG_NAME),
            'shutdown' => PerformDropletActionsByTagRequest::shutdown(self::TAG_NAME),
            'enableIpv6' => PerformDropletActionsByTagRequest::enableIpv6(self::TAG_NAME),
            'enableBackups' => PerformDropletActionsByTagRequest::enableBackups(self::TAG_NAME),
            'disableBackups' => PerformDropletActionsByTagRequest::disableBackups(self::TAG_NAME),
            default => throw new \InvalidArgumentException("Unknown factory method: {$factoryMethod}"),
        };
        $this->assertInstanceOf(PerformDropletActionsByTagRequest::class, $request);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals($expectedType, $options['json']['type']);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function factoryMethodsProvider(): array
    {
        return [
            'reboot' => ['reboot', 'reboot'],
            'powerOff' => ['powerOff', 'power_off'],
            'powerOn' => ['powerOn', 'power_on'],
            'shutdown' => ['shutdown', 'shutdown'],
            'enableIpv6' => ['enableIpv6', 'enable_ipv6'],
            'enableBackups' => ['enableBackups', 'enable_backups'],
            'disableBackups' => ['disableBackups', 'disable_backups'],
        ];
    }

    public function testSnapshotFactory(): void
    {
        $name = 'snapshot-name';

        $request = PerformDropletActionsByTagRequest::snapshot(self::TAG_NAME, $name);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals('snapshot', $options['json']['type']);
        $this->assertEquals($name, $options['json']['name']);
    }

    public function testTagNameEncoding(): void
    {
        $tagName = 'tag with spaces';
        $request = new PerformDropletActionsByTagRequest($tagName, DropletActionType::REBOOT);

        $this->assertEquals('/droplets/actions?tag_name=' . urlencode($tagName), $request->getRequestPath());
    }
}
