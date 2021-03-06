<?php

declare(strict_types=1);

namespace Lullabot\Jornada\Command;

use Lullabot\Jornada\TeamCalculatorCsvFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony Console command for calculating working days.
 */
class WorkingDaysCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        // @todo add CSV, formatted table, and JSON outputs
        $this->setName('member:report')
            ->setDescription('Generate a report of working days for team members')
            ->addArgument('end-date', InputArgument::REQUIRED, 'The end date to calculate the report to, in YYYY-MM-DD format.')
            ->addArgument('people', InputArgument::REQUIRED, 'A list of people on the project, one per line.')
            ->addOption('booked-pto', '-b', InputOption::VALUE_OPTIONAL, 'Path to booked PTO CSV with columns <person>,<day>.', '')
            ->addOption('owed-pto', '-o', InputOption::VALUE_OPTIONAL, 'Path to owed PTO CSV with columns <person>,<type>,<day>.', '')
            ->addOption('start-date', '-s', InputArgument::OPTIONAL, 'The start date to calculate the report from, in YYYY-MM-DD format.', (new \DateTimeImmutable())->format('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDate = \DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $input->getOption('start-date')
        );
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d', $input->getArgument('end-date'));

        $bookedPtoPath = $input->getOption('booked-pto');
        if (!empty($bookedPtoPath) && !file_exists($bookedPtoPath) && !is_readable($bookedPtoPath)) {
            throw new \RuntimeException(sprintf('%s does not exist or is not readable.', $bookedPtoPath));
        }

        $owedPtoPath = $input->getOption('owed-pto');
        if (!empty($owedPtoPath) && !file_exists($owedPtoPath) && !is_readable($owedPtoPath)) {
            throw new \RuntimeException(sprintf('%s does not exist or is not readable.', $owedPtoPath));
        }

        $bookedPto = $owedPto = null;
        $factory = new TeamCalculatorCsvFactory();
        if ($bookedPtoPath) {
            $bookedPto = new \SplFileObject($bookedPtoPath);
        }
        if ($owedPtoPath) {
            $owedPto = new \SplFileObject($owedPtoPath);
        }

        $teamCalculator = $factory->fromCsv(new \SplFileObject($input->getArgument('people')),
            $bookedPto,
            $owedPto
        );

        $output->writeln(
            sprintf(
                'Team business days: %s',
                $teamCalculator->getBusinessDays($startDate, $endDate)
            )
        );

        foreach ($teamCalculator->getIndividualResults($startDate, $endDate) as $individualResult) {
            $output->writeln($individualResult);
        }

        $output->writeln(sprintf('Total team working days: %s', $teamCalculator->getWorkingDays($startDate, $endDate)));

        return 0;
    }
}
