<?php

declare(strict_types=1);

namespace KimaiPlugin\CustomFieldsBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/** Explicit opt-in cleanup. Never delete source data on plugin upgrade. */
#[AsCommand(name: 'kimai:custom-fields:remove-legacy-dependents', description: 'Audit and optionally remove legacy Kimai dependents values')]
final class RemoveLegacyDependentsCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('apply', null, InputOption::VALUE_NONE, 'Remove obsolete Kimai dependents preferences');
        $this->addOption(
            'profile-fields-verified',
            null,
            InputOption::VALUE_NONE,
            'Confirm every affected cooperator has an audited Nextcloud Profile Fields value'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $before = (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM kimai2_user_preferences WHERE name = :name',
            ['name' => 'dependents']
        );
        $output->writeln(sprintf('Legacy Kimai dependents preferences: %d', $before));

        if (!$input->getOption('apply')) {
            $output->writeln('Read-only audit. No records changed. Reconcile every Nextcloud profile first.');
            return Command::SUCCESS;
        }
        if (!$input->getOption('profile-fields-verified')) {
            $output->writeln('<error>Missing --profile-fields-verified. Audit Nextcloud before removing values.</error>');
            return Command::INVALID;
        }
        $deleted = $this->connection->transactional(static function (Connection $db): int {
            return $db->executeStatement(
                'DELETE FROM kimai2_user_preferences WHERE name = :name',
                ['name' => 'dependents']
            );
        });
        $after = (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM kimai2_user_preferences WHERE name = :name',
            ['name' => 'dependents']
        );
        if ($after !== 0) {
            $output->writeln('<error>Legacy values remain present. Review database consistency.</error>');
            return Command::FAILURE;
        }
        $output->writeln(sprintf('Deleted %d obsolete dependents preferences with explicit confirmation.', $deleted));
        return Command::SUCCESS;
    }
}
