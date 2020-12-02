<?php

namespace Lullabot\Jornada\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class WorkingDaysCommandTest extends TestCase
{
    const PEOPLE = __DIR__.'/../../../fixtures/csv/people.csv';
    const BOOKED = __DIR__.'/../../../fixtures/csv/booked-pto.csv';
    const OWED = __DIR__.'/../../../fixtures/csv/owed-pto.csv';

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

    private function runOrFail(Process $process): void
    {
        $error = $process->run();
        if ($error) {
            $all = $process->getOutput().$process->getErrorOutput();
            $this->fail($all);
        }
    }
}
