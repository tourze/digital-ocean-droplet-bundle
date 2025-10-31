# DigitalOcean Droplet Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen)](#)

[English](README.md) | [中文](README.zh-CN.md)

用于管理 DigitalOcean Droplets 的 Symfony 包，提供全面的 API 集成和管理界面。

## 功能特性

- **Droplet 管理**: 创建、列出、获取和删除 DigitalOcean droplets
- **操作支持**: 执行 droplet 操作（重启、电源循环、关机等）
- **SSH 密钥管理**: 列出和管理用于 droplet 访问的 SSH 密钥
- **管理界面**: EasyAdmin 集成，提供基于 Web 的管理功能
- **数据库集成**: Doctrine ORM 集成，支持本地 droplet 存储
- **完整 API**: 全面支持 DigitalOcean API v2

## 安装

```bash
composer require tourze/digital-ocean-droplet-bundle
```

将包添加到您的 `config/bundles.php`：

```php
<?php

return [
    // ... 其他包
    DigitalOceanDropletBundle\DigitalOceanDropletBundle::class => ['all' => true],
];
```

## 依赖项

该包需要以下依赖：
- `tourze/digital-ocean-account-bundle` - 用于 DigitalOcean API 认证
- `easycorp/easyadmin-bundle` - 用于管理界面集成
- `doctrine/orm` - 用于数据库持久化

## 使用方法

### 基本用法

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
        
        // Droplet 创建成功
    }

    public function listDroplets(): array
    {
        return $this->dropletService->listDroplets(page: 1, perPage: 20);
    }
}
```

### 服务配置

该包需要通过 `tourze/digital-ocean-account-bundle` 进行 DigitalOcean API 配置。

### 管理界面

该包为通过 Web 界面管理 droplets 提供了 EasyAdmin CRUD 控制器。
通过您的管理面板访问它。

## 可用服务

- `DropletService` - Droplet 操作的主要服务
- `DropletActionService` - 执行 droplet 操作的服务
- `SSHKeyService` - SSH 密钥管理服务
- `AdminMenu` - EasyAdmin 菜单集成

## API 方法

### Droplet 操作

- `listDroplets(int $page = 1, int $perPage = 20, ?string $tagName = null)`
- `getDroplet(int $dropletId)`
- `createDroplet(string $name, string $region, string $size, array $tags = [])`
- `deleteDroplet(int $dropletId)`
- `syncDroplets()`
- `waitForDropletStatus(int $dropletId, string $expectedStatus, int $maxAttempts = 60, int $interval = 10)`

### Droplet 操作

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

### SSH 密钥管理

- `getSSHKeyIds()`

## 实体结构

`Droplet` 实体包括：
- 基本 droplet 信息（名称、内存、vcpus、磁盘）
- 网络和卷配置
- 状态和元数据
- 时间戳和标签

## 高级用法

### 同步 Droplets

将所有 droplets 从 DigitalOcean 同步到本地数据库：

```php
$droplets = $this->dropletService->syncDroplets();
```

### 等待 Droplet 状态

等待 droplet 达到特定状态并获取其公网 IP：

```php
$publicIp = $this->dropletService->waitForDropletStatus(
    dropletId: 123456,
    expectedStatus: 'active',
    maxAttempts: 60,
    interval: 10
);
```

### 自定义 Droplet 操作

```php
// 重启 droplet
$action = $this->dropletActionService->rebootDroplet(123456);

// 对带有标签的所有 droplets 执行操作
$actions = $this->dropletActionService->powerOffDropletsByTag('production');

// 等待操作完成
$completedAction = $this->dropletActionService->waitForActionCompletion(
    dropletId: 123456,
    actionId: $action['id'],
    expectedStatus: ['completed']
);
```

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/digital-ocean-droplet-bundle/tests
```

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+
- EasyAdmin 4+

## 许可证

MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。