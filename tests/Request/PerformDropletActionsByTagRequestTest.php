<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Enum\DropletActionType;
use DigitalOceanDropletBundle\Request\PerformDropletActionsByTagRequest;
use PHPUnit\Framework\TestCase;

class PerformDropletActionsByTagRequestTest extends TestCase
{
    private const TAG_NAME = 'web-servers';
    
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
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertEquals('reboot', $options['json']['type']);
    }
    
    public function testConstructorWithStringActionType(): void
    {
        $request = new PerformDropletActionsByTagRequest(self::TAG_NAME, 'reboot');
        $options = $request->getRequestOptions();
        
        $this->assertEquals('reboot', $options['json']['type']);
    }
    
    /**
     * @dataProvider factoryMethodsProvider
     */
    public function testFactoryMethods(string $factoryMethod, string $expectedType): void
    {
        $request = call_user_func([PerformDropletActionsByTagRequest::class, $factoryMethod], self::TAG_NAME);
        
        $options = $request->getRequestOptions();
        $this->assertEquals($expectedType, $options['json']['type']);
    }
    
    public function factoryMethodsProvider(): array
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