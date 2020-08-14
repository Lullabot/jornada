<?php

declare(strict_types=1);

namespace Lullabot\Jornada;

/**
 * Calculates working days based on typical 5 day working weeks with holidays.
 *
 * PHP does not have support for "date only" date objects, so this class
 * ignores all times included with the dates except for the purposes of shifting
 * dates by time zones.
 */
class WorkingDaysCalculator
{
    /**
     * @var \DateTimeInterface[]
     */
    private $holidays = [];

    /**
     * Set holiday days, in addition to weekends.
     *
     * @param \DateTimeInterface[] $holidays
     */
    public function setHolidays(array $holidays): void
    {
        $this->holidays = $holidays;
    }

    /**
     * Add a new holiday date.
     */
    public function addHoliday(\DateTimeInterface $dateTime)
    {
        $this->holidays[] = $dateTime;
    }

    /**
     * Get the working days between two dates, including those days.
     */
    public function getWorkingDays(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        if ($endDate < $startDate) {
            throw new \InvalidArgumentException(sprintf('The end date must not be before the start date'));
        }

        // By default diff and intervals don't include the last date. Extend
        // by one day so we are inclusive of the end date. We need to clone in
        // the case that $endDate is a \DateTime and not \DateTimeImmutable.
        $cloned = clone $endDate;
        $cloned = $cloned->modify('+1 day');

        // Include start and end, as we check them below for if they are working
        // days.
        // https://stackoverflow.com/questions/30446918/what-is-the-difference-between-the-days-and-d-property-in-dateinterval
        // documents the difference between 'd' (days past number of months) and
        // 'days' which is the total number of days.
        $days = $startDate->diff($cloned)->days;

        $period = new \DatePeriod(
            $startDate, new \DateInterval('P1D'), $cloned
        );
        foreach ($period as $dt) {
            if (!$this->isWorkingDay($dt)) {
                --$days;
            }
        }
        return $days;
    }

    /**
     * Return if a given date is a working day.
     *
     * A working day is not a weekend (M-F) and not a holiday.
     */
    public function isWorkingDay(\DateTimeInterface $date): bool
    {
        return !$this->isWeekend($date) && !$this->isHoliday($date);
    }

    /**
     * Return if a given date is on the weekend.
     */
    public function isWeekend(\DateTimeInterface $date): bool
    {
        return \in_array($date->format('D'), ['Sat', 'Sun']);
    }

    /**
     * Return if a given date is a holiday.
     *
     * Holidays change yearly per region, so they must be set by calling one of
     * the holiday methods.
     */
    public function isHoliday(\DateTimeInterface $date): bool
    {
        if (!$this->holidays) {
            return false;
        }

        $holidays = array_map(function (\DateTimeInterface $value) {
            return $value->format('Y-m-d');
        }, $this->holidays);

        return \in_array($date->format('Y-m-d'), $holidays);
    }

    /**
     * Add the given number of working days and return a new date.
     */
    public function addDays(\DateTimeInterface $date, int $days): \DateTimeImmutable
    {
        $daysAdded = clone $date;
        if ($date instanceof \DateTime) {
            $daysAdded = \DateTimeImmutable::createFromMutable($date);
        }

        for ($i = 0; $i < $days; ++$i) {
            $daysAdded = $daysAdded->modify('+1 day');

            // Push ahead for any holidays and weekends.
            while (!$this->isWorkingDay($daysAdded)) {
                $daysAdded = $daysAdded->modify('+1 day');
            }
        }

        return $daysAdded;
    }
}
