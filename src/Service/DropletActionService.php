<?php

namespace DigitalOceanDropletBundle\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanDropletBundle\Enum\DropletActionType;
use DigitalOceanDropletBundle\Exception\DigitalOceanConfigurationException;
use DigitalOceanDropletBundle\Request\GetDropletActionRequest;
use DigitalOceanDropletBundle\Request\ListDropletActionsRequest;
use DigitalOceanDropletBundle\Request\PerformDropletActionRequest;
use DigitalOceanDropletBundle\Request\PerformDropletActionsByTagRequest;
use Psr\Log\LoggerInterface;

class DropletActionService
{
    public function __construct(
        private readonly DigitalOceanClient $client,
        private readonly DigitalOceanConfigService $configService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 为请求设置API Key
     */
    private function prepareRequest($request): void
    {
        $config = $this->configService->getConfig();
        if ($config === null) {
            throw new DigitalOceanConfigurationException('未配置 DigitalOcean API Key');
        }

        $request->setApiKey($config->getApiKey());
    }

    /**
     * 获取Droplet的操作记录列表
     */
    public function listDropletActions(int $dropletId, int $page = 1, int $perPage = 20): array
    {
        $request = (new ListDropletActionsRequest($dropletId))
            ->setPage($page)
            ->setPerPage($perPage);

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        return [
            'actions' => $response['actions'] ?? [],
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ];
    }

    /**
     * 获取指定操作的详情
     */
    public function getDropletAction(int $dropletId, int $actionId): array
    {
        $request = new GetDropletActionRequest($dropletId, $actionId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        return $response['action'] ?? [];
    }

    /**
     * 执行重启Droplet操作
     */
    public function rebootDroplet(int $dropletId): array
    {
        $request = PerformDropletActionRequest::reboot($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机重启操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 执行关闭Droplet电源操作
     */
    public function powerOffDroplet(int $dropletId): array
    {
        $request = PerformDropletActionRequest::powerOff($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机关闭电源操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 执行开启Droplet电源操作
     */
    public function powerOnDroplet(int $dropletId): array
    {
        $request = PerformDropletActionRequest::powerOn($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机开启电源操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 执行关机操作
     */
    public function shutdownDroplet(int $dropletId): array
    {
        $request = PerformDropletActionRequest::shutdown($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机关机操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 启用IPv6功能
     */
    public function enableIpv6(int $dropletId): array
    {
        $request = PerformDropletActionRequest::enableIpv6($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机启用IPv6操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 启用备份功能
     */
    public function enableBackups(int $dropletId): array
    {
        $request = PerformDropletActionRequest::enableBackups($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机启用备份操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 禁用备份功能
     */
    public function disableBackups(int $dropletId): array
    {
        $request = PerformDropletActionRequest::disableBackups($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机禁用备份操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 重置密码
     */
    public function resetPassword(int $dropletId): array
    {
        $request = PerformDropletActionRequest::passwordReset($dropletId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机重置密码操作已执行', [
            'dropletId' => $dropletId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 重建Droplet
     */
    public function rebuildDroplet(int $dropletId, int $imageId): array
    {
        $request = (new PerformDropletActionRequest($dropletId, DropletActionType::REBUILD))
            ->rebuild($imageId);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机重建操作已执行', [
            'dropletId' => $dropletId,
            'imageId' => $imageId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 调整Droplet大小
     */
    public function resizeDroplet(int $dropletId, string $size, bool $resizeDisk = true): array
    {
        $request = (new PerformDropletActionRequest($dropletId, DropletActionType::RESIZE))
            ->resize($size, $resizeDisk);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机调整大小操作已执行', [
            'dropletId' => $dropletId,
            'size' => $size,
            'resizeDisk' => $resizeDisk,
        ]);

        return $response['action'] ?? [];
    }
    
    /**
     * 重命名Droplet
     */
    public function renameDroplet(int $dropletId, string $name): array
    {
        $request = (new PerformDropletActionRequest($dropletId, DropletActionType::RENAME))
            ->rename($name);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机重命名操作已执行', [
            'dropletId' => $dropletId,
            'name' => $name,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 为Droplet创建快照
     */
    public function snapshotDroplet(int $dropletId, string $name): array
    {
        $request = PerformDropletActionRequest::snapshot($dropletId, $name);
        
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机创建快照操作已执行', [
            'dropletId' => $dropletId,
            'name' => $name,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 从快照恢复Droplet
     */
    public function restoreDroplet(int $dropletId, int $imageId): array
    {
        $request = PerformDropletActionRequest::restore($dropletId, $imageId);

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机从快照恢复操作已执行', [
            'dropletId' => $dropletId,
            'imageId' => $imageId,
        ]);

        return $response['action'] ?? [];
    }

    /**
     * 根据标签批量重启Droplet
     */
    public function rebootDropletsByTag(string $tagName): array
    {
        $request = PerformDropletActionsByTagRequest::reboot($tagName);

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机批量重启操作已执行', [
            'tagName' => $tagName,
        ]);

        return $response['actions'] ?? [];
    }

    /**
     * 根据标签批量关闭Droplet电源
     */
    public function powerOffDropletsByTag(string $tagName): array
    {
        $request = PerformDropletActionsByTagRequest::powerOff($tagName);

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机批量关闭电源操作已执行', [
            'tagName' => $tagName,
        ]);

        return $response['actions'] ?? [];
    }

    /**
     * 根据标签批量开启Droplet电源
     */
    public function powerOnDropletsByTag(string $tagName): array
    {
        $request = PerformDropletActionsByTagRequest::powerOn($tagName);

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机批量开启电源操作已执行', [
            'tagName' => $tagName,
        ]);

        return $response['actions'] ?? [];
    }

    /**
     * 根据标签批量为Droplet创建快照
     */
    public function snapshotDropletsByTag(string $tagName, string $name): array
    {
        $request = PerformDropletActionsByTagRequest::snapshot($tagName, $name);

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机批量创建快照操作已执行', [
            'tagName' => $tagName,
            'name' => $name,
        ]);

        return $response['actions'] ?? [];
    }

    /**
     * 等待操作完成
     *
     * @param int $dropletId Droplet ID
     * @param int $actionId 操作ID
     * @param array $expectedStatus 期望的状态，默认为 completed
     * @param int $maxAttempts 最大尝试次数
     * @param int $interval 检查间隔（秒）
     * @return array|null 操作详情，如果未完成则返回null
     */
    public function waitForActionCompletion(
        int $dropletId, 
        int $actionId, 
        array $expectedStatus = ['completed'], 
        int $maxAttempts = 30, 
        int $interval = 10
    ): ?array {
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $actionData = $this->getDropletAction($dropletId, $actionId);
                $status = $actionData['status'] ?? '';

                if (in_array($status, $expectedStatus)) {
                    return $actionData;
                }

                // 如果操作状态是errored，则停止等待
                if ($status === 'errored') {
                    $this->logger->error('DigitalOcean虚拟机操作出错', [
                        'dropletId' => $dropletId,
                        'actionId' => $actionId,
                        'action' => $actionData,
                    ]);
                    return $actionData;
                }

                $attempt++;
                sleep($interval);
            } catch (\Throwable $e) {
                $this->logger->error('检查DigitalOcean虚拟机操作状态失败', [
                    'dropletId' => $dropletId,
                    'actionId' => $actionId,
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                ]);
                $attempt++;
                sleep($interval);
            }
        }

        $this->logger->warning('等待DigitalOcean虚拟机操作完成超时', [
            'dropletId' => $dropletId,
            'actionId' => $actionId,
        ]);

        return null;
    }
}
