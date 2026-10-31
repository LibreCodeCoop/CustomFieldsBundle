<?php

declare(strict_types=1);

namespace KimaiPlugin\CustomFieldsBundle\Tests;

use Doctrine\DBAL\Connection;
use KimaiPlugin\CustomFieldsBundle\Command\RemoveLegacyDependentsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class RemoveLegacyDependentsCommandTest extends TestCase
{
    public function testDryRunDoesNotDeleteAnything(): void
    {
        $db = $this->createMock(Connection::class);
        $db->expects(self::once())->method('fetchOne')->willReturn(3);
        $db->expects(self::never())->method('transactional');
        $db->expects(self::never())->method('executeStatement');

        $tester = new CommandTester(new RemoveLegacyDependentsCommand($db));
        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertStringContainsString('3', $tester->getDisplay());
        self::assertStringContainsString('No records changed', $tester->getDisplay());
    }

    public function testApplyRequiresExplicitVerifiedNextcloudCoverage(): void
    {
        $db = $this->createMock(Connection::class);
        $db->expects(self::once())->method('fetchOne')->willReturn(3);
        $db->expects(self::never())->method('executeStatement');

        $tester = new CommandTester(new RemoveLegacyDependentsCommand($db));
        self::assertSame(Command::INVALID, $tester->execute(['--apply' => true]));
        self::assertStringContainsString('--profile-fields-verified', $tester->getDisplay());
    }

    public function testAcknowledgedCleanupDeletesOnlyNamedPreference(): void
    {
        $db = $this->createMock(Connection::class);
        $db->expects(self::exactly(2))->method('fetchOne')
            ->with('SELECT COUNT(*) FROM kimai2_user_preferences WHERE name = :name', ['name' => 'dependents'])
            ->willReturnOnConsecutiveCalls(3, 0);
        $db->expects(self::once())->method('transactional')
            ->willReturnCallback(static fn (callable $fn): int => $fn($db));
        $db->expects(self::once())->method('executeStatement')
            ->with('DELETE FROM kimai2_user_preferences WHERE name = :name', ['name' => 'dependents'])
            ->willReturn(3);

        $tester = new CommandTester(new RemoveLegacyDependentsCommand($db));
        self::assertSame(Command::SUCCESS, $tester->execute([
            '--apply' => true,
            '--profile-fields-verified' => true,
        ]));
        self::assertStringContainsString('Deleted 3', $tester->getDisplay());
    }
}
