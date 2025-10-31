<?php

namespace DigitalOceanDropletBundle\Tests\Enum;

use DigitalOceanDropletBundle\Enum\DropletActionType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DropletActionType::class)]
final class DropletActionTypeTest extends AbstractEnumTestCase
{
    #[TestWith(['reboot', '重启'])]
    #[TestWith(['power_off', '关闭电源'])]
    #[TestWith(['power_on', '开启电源'])]
    #[TestWith(['shutdown', '关机'])]
    #[TestWith(['restore', '还原'])]
    #[TestWith(['password_reset', '重置密码'])]
    #[TestWith(['resize', '调整大小'])]
    #[TestWith(['rebuild', '重建'])]
    #[TestWith(['rename', '重命名'])]
    #[TestWith(['change_kernel', '更改内核'])]
    #[TestWith(['enable_ipv6', '启用IPv6'])]
    #[TestWith(['enable_backups', '启用备份'])]
    #[TestWith(['disable_backups', '禁用备份'])]
    #[TestWith(['enable_private_networking', '启用私有网络'])]
    #[TestWith(['snapshot', '创建快照'])]
    public function testValueAndLabel(string $expectedValue, string $expectedLabel): void
    {
        $enum = DropletActionType::from($expectedValue);
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testTryFromWithValidValue(): void
    {
        $result = DropletActionType::tryFrom('reboot');
        $this->assertSame(DropletActionType::REBOOT, $result);
    }

    public function testTryFromWithEmptyStringReturnsNull(): void
    {
        $result = DropletActionType::tryFrom('');
        $this->assertNull($result);
    }

    public function testValueUniqueness(): void
    {
        $cases = DropletActionType::cases();
        $values = array_map(static fn (DropletActionType $case) => $case->value, $cases);

        $this->assertSame(count($values), count(array_unique($values)), 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $cases = DropletActionType::cases();
        $labels = array_map(static fn (DropletActionType $case) => $case->getLabel(), $cases);

        $this->assertSame(count($labels), count(array_unique($labels)), 'All enum labels must be unique');
    }

    public function testAllCasesHaveValidValues(): void
    {
        foreach (DropletActionType::cases() as $case) {
            $this->assertNotEmpty($case->value, "Case {$case->name} must have a non-empty value");
            $this->assertNotEmpty($case->getLabel(), "Case {$case->name} must have a non-empty label");
        }
    }

    public function testToArray(): void
    {
        $case = DropletActionType::REBOOT;
        $array = $case->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('reboot', $array['value']);
        $this->assertSame('重启', $array['label']);

        // 测试其他枚举值
        $powerOffArray = DropletActionType::POWER_OFF->toArray();
        $this->assertSame('power_off', $powerOffArray['value']);
        $this->assertSame('关闭电源', $powerOffArray['label']);
    }
}
