<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Enum\DropletActionType;
use DigitalOceanDropletBundle\Request\PerformDropletActionRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversClass(PerformDropletActionRequest::class)]
final class PerformDropletActionRequestTest extends RequestTestCase
{
    private const DROPLET_ID = 12345;

    protected function setUp(): void
    {
        parent::setUp();
        // 测试设置
    }

    public function testGetRequestPath(): void
    {
        $request = new PerformDropletActionRequest(self::DROPLET_ID, DropletActionType::REBOOT);
        $this->assertEquals('/droplets/' . self::DROPLET_ID . '/actions', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new PerformDropletActionRequest(self::DROPLET_ID, DropletActionType::REBOOT);
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new PerformDropletActionRequest(self::DROPLET_ID, DropletActionType::REBOOT);
        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertEquals('reboot', $options['json']['type']);
    }

    public function testConstructorWithStringActionType(): void
    {
        $request = new PerformDropletActionRequest(self::DROPLET_ID, 'reboot');
        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);

        $this->assertEquals('reboot', $options['json']['type']);
    }

    #[DataProvider('factoryMethodsProvider')]
    public function testFactoryMethods(string $factoryMethod, string $expectedType): void
    {
        $request = match ($factoryMethod) {
            'reboot' => PerformDropletActionRequest::reboot(self::DROPLET_ID),
            'powerOff' => PerformDropletActionRequest::powerOff(self::DROPLET_ID),
            'powerOn' => PerformDropletActionRequest::powerOn(self::DROPLET_ID),
            'shutdown' => PerformDropletActionRequest::shutdown(self::DROPLET_ID),
            'enableIpv6' => PerformDropletActionRequest::enableIpv6(self::DROPLET_ID),
            'enableBackups' => PerformDropletActionRequest::enableBackups(self::DROPLET_ID),
            'disableBackups' => PerformDropletActionRequest::disableBackups(self::DROPLET_ID),
            'passwordReset' => PerformDropletActionRequest::passwordReset(self::DROPLET_ID),
            default => throw new \InvalidArgumentException("Unknown factory method: {$factoryMethod}"),
        };
        $this->assertInstanceOf(PerformDropletActionRequest::class, $request);

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
            'passwordReset' => ['passwordReset', 'password_reset'],
        ];
    }

    public function testRebuildMethod(): void
    {
        $imageId = 54321;
        $request = new PerformDropletActionRequest(self::DROPLET_ID, DropletActionType::REBUILD);
        $request->rebuild($imageId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals('rebuild', $options['json']['type']);
        $this->assertEquals($imageId, $options['json']['image']);
    }

    public function testResizeMethod(): void
    {
        $size = 's-2vcpu-2gb';
        $disk = true;

        $request = new PerformDropletActionRequest(self::DROPLET_ID, DropletActionType::RESIZE);
        $request->resize($size, $disk);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals('resize', $options['json']['type']);
        $this->assertEquals($size, $options['json']['size']);
        $this->assertEquals($disk, $options['json']['disk']);
    }

    public function testRenameMethod(): void
    {
        $name = 'new-droplet-name';

        $request = new PerformDropletActionRequest(self::DROPLET_ID, DropletActionType::RENAME);
        $request->rename($name);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals('rename', $options['json']['type']);
        $this->assertEquals($name, $options['json']['name']);
    }

    public function testSnapshotFactory(): void
    {
        $name = 'snapshot-name';

        $request = PerformDropletActionRequest::snapshot(self::DROPLET_ID, $name);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals('snapshot', $options['json']['type']);
        $this->assertEquals($name, $options['json']['name']);
    }

    public function testRestoreFactory(): void
    {
        $imageId = 54321;

        $request = PerformDropletActionRequest::restore(self::DROPLET_ID, $imageId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options['json']);
        $this->assertEquals('restore', $options['json']['type']);
        $this->assertEquals($imageId, $options['json']['image']);
    }
}
