<?php

namespace Lullabot\Jornada\Tests\Unit;

use Lullabot\Jornada\TeamCalculatorCsvFactory;
use PHPUnit\Framework\TestCase;

class TeamCalculatorCsvFactoryTest extends TestCase
{
    public function testFactory()
    {
        $factory = new TeamCalculatorCsvFactory();
        $people = new \SplFileObject(__DIR__.'/../../fixtures/csv/single/people.csv');
        $booked = new \SplFileObject(__DIR__.'/../../fixtures/csv/single/booked-pto.csv');
        $owed = new \SplFileObject(__DIR__.'/../../fixtures/csv/single/owed-pto.csv');
        $calculator = $factory->fromCsv($people, $booked, $owed);
        $start = \DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2020-11-30'
        );
        $end = \DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2020-12-04'
        );
        $this->assertEquals(2, $calculator->getWorkingDays($start, $end));
    }

    public function testMultipleTeamMembers()
    {
        $factory = new TeamCalculatorCsvFactory();
        $people = new \SplFileObject(__DIR__.'/../../fixtures/csv/multiple/people.csv');
        $booked = new \SplFileObject(__DIR__.'/../../fixtures/csv/multiple/booked-pto.csv');
        $calculator = $factory->fromCsv($people, $booked);
        $start = \DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2020-11-30'
        );
        $end = \DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2020-12-04'
        );
        $this->assertEquals(19, $calculator->getWorkingDays($start, $end));
    }
}
