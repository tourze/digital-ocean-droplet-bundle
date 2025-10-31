# DigitalOcean Droplet Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen)](#)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for managing DigitalOcean Droplets with comprehensive API integration and admin interface.

## Features

- **Droplet Management**: Create, list, retrieve, and delete DigitalOcean droplets
- **Actions Support**: Perform droplet actions (reboot, power cycle, shutdown, etc.)
- **SSH Key Management**: List and manage SSH keys for droplet access
- **Admin Interface**: EasyAdmin integration for web-based management
- **Database Integration**: Doctrine ORM integration with local droplet storage
- **Comprehensive API**: Full support for DigitalOcean API v2

## Installation

```bash
composer require tourze/digital-ocean-droplet-bundle
```

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    DigitalOceanDropletBundle\DigitalOceanDropletBundle::class => ['all' => true],
];
```

## Dependencies

This bundle requires:
- `tourze/digital-ocean-account-bundle` - For DigitalOcean API authentication
- `easycorp/easyadmin-bundle` - For admin interface integration
- `doctrine/orm` - For database persistence

## Quick Start

### Basic Usage

```php
<?php

use DigitalOceanDropletBundle\Service\DropletService;
use DigitalOceanDropletBundle\Request\CreateDropletRequest;

class MyController
{
    public function __construct(
        private readonly DropletService $dropletService
    ) {}

    public function createDroplet(): void
    {
        $droplet = $this->dropletService->createDroplet(
            name: 'my-droplet',
            region: 'nyc3',
            size: 's-1vcpu-1gb',
            tags: ['production']
        );
        
        // Droplet created successfully
    }

    public function listDroplets(): array
    {
        return $this->dropletService->listDroplets(page: 1, perPage: 20);
    }
}
```

### Service Configuration

The bundle requires DigitalOcean API configuration through the `tourze/digital-ocean-account-bundle`.

### Admin Interface

The bundle provides an EasyAdmin CRUD controller for managing droplets through the web interface.
Access it through your admin panel.

## Available Services

- `DropletService` - Main service for droplet operations
- `DropletActionService` - Service for performing droplet actions
- `SSHKeyService` - Service for SSH key management
- `AdminMenu` - EasyAdmin menu integration

## API Methods

### Droplet Operations

- `listDroplets(int $page = 1, int $perPage = 20, ?string $tagName = null)`
- `getDroplet(int $dropletId)`
- `createDroplet(string $name, string $region, string $size, array $tags = [])`
- `deleteDroplet(int $dropletId)`
- `syncDroplets()`
- `waitForDropletStatus(int $dropletId, string $expectedStatus, int $maxAttempts = 60, int $interval = 10)`

### Droplet Actions

- `getDropletAction(int $dropletId, int $actionId)`
- `listDropletActions(int $dropletId, int $page = 1, int $perPage = 20)`
- `rebootDroplet(int $dropletId)`
- `powerOnDroplet(int $dropletId)`
- `powerOffDroplet(int $dropletId)`
- `shutdownDroplet(int $dropletId)`
- `rebootDropletsByTag(string $tagName)`
- `powerOnDropletsByTag(string $tagName)`
- `powerOffDropletsByTag(string $tagName)`
- `waitForActionCompletion(int $dropletId, int $actionId, array $expectedStatus = ['completed'], int $maxAttempts = 60, int $interval = 10)`

### SSH Key Management

- `getSSHKeyIds()`

## Entity Structure

The `Droplet` entity includes:
- Basic droplet information (name, memory, vcpus, disk)
- Network and volume configurations
- Status and metadata
- Timestamps and tags

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/digital-ocean-droplet-bundle/tests
```

## Advanced Usage

### Synchronizing Droplets

Sync all droplets from DigitalOcean to your local database:

```php
$droplets = $this->dropletService->syncDroplets();
```

### Waiting for Droplet Status

Wait for a droplet to reach a specific status and get its public IP:

```php
$publicIp = $this->dropletService->waitForDropletStatus(
    dropletId: 123456,
    expectedStatus: 'active',
    maxAttempts: 60,
    interval: 10
);
```

### Custom Droplet Actions

```php
// Reboot a droplet
$action = $this->dropletActionService->rebootDroplet(123456);

// Perform action on all droplets with a tag
$actions = $this->dropletActionService->powerOffDropletsByTag('production');

// Wait for action completion
$completedAction = $this->dropletActionService->waitForActionCompletion(
    dropletId: 123456,
    actionId: $action['id'],
    expectedStatus: ['completed']
);
```

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- EasyAdmin 4+

## License

MIT License. See [LICENSE](LICENSE) for details.