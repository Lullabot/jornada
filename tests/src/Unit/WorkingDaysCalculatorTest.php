<?php

declare(strict_types=1);

namespace Lullabot\Jornada\Tests\Unit;

use Lullabot\Jornada\WorkingDaysCalculator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lullabot\Jornada\WorkingDaysCalculator
 */
class WorkingDaysCalculatorTest extends TestCase {

    public function testGetWorkingDays() {
        // Test 5 days in a week.
        $monday = \DateTime::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTime::createFromFormat('Y-m-d', '2020-06-12');
        $calculator = new WorkingDaysCalculator();
        $this->assertEquals(5, $calculator->getWorkingDays($monday, $friday));
    }

    public function testGetWorkingDaysImmutable() {
        // Test 5 days in a week.
        $monday = \DateTimeImmutable::createFromFormat('Y-m-d', '2020-06-08');
        $friday = \DateTimeImmutable::createFromFormat('Y-m-d', '2020-06-12');
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
        $calculator->addHoliday(\DateTimeImmutable::createFromFormat('Y-m-d', '2020-06-10'));
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
      $startDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2020-08-17');
      $endDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2020-12-31');
      $this->assertEquals(99, $calc->getWorkingDays($startDate, $endDate));
    }
}
