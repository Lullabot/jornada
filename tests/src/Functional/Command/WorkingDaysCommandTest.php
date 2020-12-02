<?php

namespace Lullabot\Jornada\Tests\Functional\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class WorkingDaysCommandTest extends TestCase
{
    const PEOPLE = __DIR__.'/../../../fixtures/csv/single/people.csv';
    const BOOKED = __DIR__.'/../../../fixtures/csv/single/booked-pto.csv';
    const OWED = __DIR__.'/../../../fixtures/csv/single/owed-pto.csv';

    private $console = __DIR__.'/../../../../bin/console';

    /**
     * Test calculating basic working days with no holidays booked.
     */
    public function testMemberReport()
    {
        $process = new Process([$this->console, 'member:report', '--start-date=2020-12-01', '2020-12-31',
            self::PEOPLE,
        ]);
        $this->runOrFail($process);
        $expected = <<<EOD
Team business days: 23
andrew has 23 business days remaining, 23 working days remaining, finishing on 2020-12-31.
Total team working days: 23

EOD;

        $this->assertEquals($expected, $process->getOutput());
    }

    public function testBookedPto()
    {
        $process = new Process([
            $this->console,
            'member:report',
            '--start-date=2020-12-01',
            '--booked-pto='.self::BOOKED,
            '2020-12-31',
            self::PEOPLE,
        ]);
        $this->runOrFail($process);

        $expected = <<<EOD
Team business days: 23
andrew has 23 business days remaining, 22 working days remaining, finishing on 2020-12-31.
Total team working days: 22

EOD;
        $this->assertEquals($expected, $process->getOutput());
    }

    public function testOwedPto()
    {
        $process = new Process([
            $this->console,
            'member:report',
            '--start-date=2020-11-30',
            '--owed-pto='.self::OWED,
            '2020-12-31',
            self::PEOPLE,
        ]);
        $this->runOrFail($process);

        $expected = <<<EOD
Team business days: 24
andrew has 24 business days remaining, 22 working days remaining, finishing on 2020-12-29.
Total team working days: 22

EOD;
        $this->assertEquals($expected, $process->getOutput());
    }

    public function testMultipleTeamMembers()
    {
        $process = new Process([
            $this->console,
            'member:report',
            '--start-date=2020-11-30',
            '--booked-pto='.__DIR__.'/../../../fixtures/csv/multiple/booked-pto.csv',
            '2020-12-31',
            __DIR__.'/../../../fixtures/csv/multiple/people.csv'
        ]);
        $this->runOrFail($process);

        $expected = <<<EOD
Team business days: 96
andrew has 24 business days remaining, 23 working days remaining, finishing on 2020-12-31.
amanda has 24 business days remaining, 21 working days remaining, finishing on 2020-12-31.
harry has 24 business days remaining, 23 working days remaining, finishing on 2020-12-31.
zoe has 24 business days remaining, 23 working days remaining, finishing on 2020-12-30.
Total team working days: 90

EOD;
        $this->assertEquals($expected, $process->getOutput());
    }

    private function runOrFail(Process $process): void
    {
        $error = $process->run();
        if ($error) {
            $all = $process->getOutput().$process->getErrorOutput();
            $this->fail($all);
        }
    }
}
