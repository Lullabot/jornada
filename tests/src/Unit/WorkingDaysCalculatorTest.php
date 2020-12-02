<?php

declare(strict_types=1);

namespace Lullabot\Jornada\Tests\Unit;

use Lullabot\Jornada\WorkingDaysCalculator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Jornada\WorkingDaysCalculator
 */
class WorkingDaysCalculatorTest extends TestCase
{
    public function testGetWorkingDays()
    {
        // Test 5 days in a week.
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-12');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(5, $calculator->getWorkingDays($monday, $friday));
    }

    public function testGetWorkingDaysImmutable()
    {
        // Test 5 days in a week.
        $monday = $this->createDate('2020-06-08');
        $friday = $this->createDate('2020-06-12');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(5, $calculator->getWorkingDays($monday, $friday));
    }

    public function testGetWorkingDaysTwoWeeks()
    {
        // Test 10 days in two weeks.
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-19');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(10, $calculator->getWorkingDays($monday, $friday));
    }

    public function testOneWorkingDay()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(1, $calculator->getWorkingDays($monday, $monday));
    }

    public function testTwoWorkingDays()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $tuesday = \DateTime::createFromFormat('Y-m-d', '2020-06-09');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(2, $calculator->getWorkingDays($monday, $tuesday));
    }

    public function testTwoWorkingDaysHoliday()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $tuesday = \DateTime::createFromFormat('Y-m-d', '2020-06-09');
        $calculator = new WorkingDaysCalculator();
        $calculator->addHoliday($monday);
        $this->assertEquals(1, $calculator->getWorkingDays($monday, $tuesday));
    }

    public function testZeroWorkingDays()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $calculator = new WorkingDaysCalculator();
        $calculator->addHoliday($monday);
        $this->assertEquals(0, $calculator->getWorkingDays($monday, $monday));
    }

    public function testNotMondayStart()
    {
        $tuesday = \DateTime::createFromFormat('Y-m-d', '2020-07-21');
        $nextTuesday = \DateTime::createFromFormat('Y-m-d', '2020-07-28');
        $nextMonday = \DateTime::createFromFormat('Y-m-d', '2020-07-27');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(5, $calculator->getWorkingDays($tuesday, $nextMonday));
        $this->assertEquals(6, $calculator->getWorkingDays($tuesday, $nextTuesday));
    }

    public function testHasHoliday()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-12');
        $calculator = new WorkingDaysCalculator();
        $calculator->setHolidays([$monday]);
        $this->assertEquals(4, $calculator->getWorkingDays($monday, $friday));
    }

    public function testMultipleHolidays()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-12');
        $calculator = new WorkingDaysCalculator();
        $calculator->setHolidays([$monday, $friday]);
        $this->assertEquals(3, $calculator->getWorkingDays($monday, $friday));
    }

    public function testHasWeekendHoliday()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-19');
        $calculator = new WorkingDaysCalculator();
        $calculator->addHoliday(\DateTime::createFromFormat('Y-m-d', '2020-06-13'));
        $this->assertEquals(10, $calculator->getWorkingDays($monday, $friday));
    }

    public function testEndBeforeStart()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-12');
        $calculator = new WorkingDaysCalculator();
        $this->expectException(\InvalidArgumentException::class);
        $calculator->getWorkingDays($friday, $monday);
    }

    public function testAddDays()
    {
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $calculator = new WorkingDaysCalculator();
        $calculator->addHoliday($this->createDate('2020-06-10'));
        $addOne = $calculator->addDays($monday, 1);
        $this->assertEquals('2020-06-09', $addOne->format('Y-m-d'));
        $addTwo = $calculator->addDays($monday, 2);
        $this->assertEquals('2020-06-11', $addTwo->format('Y-m-d'));
        $addFive = $calculator->addDays($monday, 5);
        $this->assertEquals('2020-06-16', $addFive->format('Y-m-d'));
    }

    public function testOverMultipleMonths()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-08-17');
        $endDate = $this->createDate('2020-12-31');
        $this->assertEquals(99, $calc->getWorkingDays($startDate, $endDate));
    }

    public function testWithUnbookedHolidays()
    {
        $calc = new WorkingDaysCalculator();
        $calc->addUnbookedHolidayDays(10);
        $startDate = $this->createDate('2020-08-17');
        $endDate = $this->createDate('2020-12-31');
        $this->assertEquals(89, $calc->getWorkingDays($startDate, $endDate));
    }

    public function testGetTotalHolidays()
    {
        $calc = new WorkingDaysCalculator();
        $this->assertEquals(0, $calc->getTotalHolidays());
        $calc->addHoliday(new \DateTimeImmutable());
        $this->assertEquals(1, $calc->getTotalHolidays());
        $calc->addUnbookedHolidayDays(2);
        $this->assertEquals(3, $calc->getTotalHolidays());
    }

    public function testGetLastDay()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-10-02');
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-10-02', $last->format('Y-m-d'));
    }

    public function testGetLastDayTwoWorkingDays()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-09-29');
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-09-29', $last->format('Y-m-d'));
    }

    public function testGetLastDayInvalidStartDate()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-27');
        $endDate = $this->createDate('2020-09-27');
        $this->expectException(\InvalidArgumentException::class);
        $calc->getLastDay($startDate, $endDate);
    }

    public function testGetFirstDayWeekend()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-26');
        $endDate = $this->createDate('2020-09-28');
        $this->assertEquals('2020-09-28', $calc->getLastDay($startDate, $endDate)->format('Y-m-d'));
    }

    public function testGetLastDayWeekend()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-25');
        $endDate = $this->createDate('2020-09-27');
        $this->assertEquals('2020-09-25', $calc->getLastDay($startDate, $endDate)->format('Y-m-d'));
    }

    public function testGetLastDayWithHolidays()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-10-09');
        $calc->addHoliday($this->createDate('2020-09-29'));
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-10-09', $last->format('Y-m-d'));
    }

    public function testGetLastDayBeforeEnd()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-10-09');
        $calc->addHoliday($this->createDate('2020-09-29'));
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-10-09', $last->format('Y-m-d'));
    }

    public function testGetLastDayBeforeProjectEnd()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-10-09');
        $calc->addUnbookedHolidayDays(1);
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-10-08', $last->format('Y-m-d'));
    }

    public function testGetLastDayBeforeProjectEndWithHoliday()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-10-09');
        $calc->addHoliday($this->createDate('2020-09-29'));
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-10-09', $last->format('Y-m-d'));
    }

    public function testGetLastDayBeforeProjectEndWithUnbookedHoliday()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-10-09');
        $calc->addUnbookedHolidayDays(1);
        $calc->addHoliday($this->createDate('2020-09-29'));
        $last = $calc->getLastDay($startDate, $endDate);
        $this->assertEquals('2020-10-08', $last->format('Y-m-d'));
    }

    public function testInvalidDateRange()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-09-28');
        $endDate = $this->createDate('2020-09-28');
        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals('', $calc->getLastDay($startDate, $endDate)->format('Y-m-d'));
    }

    public function testLastDayWithUnbookedHolidays()
    {
        $calc = new WorkingDaysCalculator();
        $startDate = $this->createDate('2020-11-30');
        $endDate = $this->createDate('2020-12-31');
        $calc->addUnbookedHolidayDays(2);
        $this->assertEquals(22, $calc->getWorkingDays($startDate, $endDate));
        $this->assertEquals('2020-12-29', $calc->getLastDay($startDate, $endDate)->format('Y-m-d'));
    }

    private function createDate($date): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d', $date);
    }
}
