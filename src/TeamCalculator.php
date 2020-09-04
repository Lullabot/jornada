<?php

declare(strict_types=1);

namespace Lullabot\Jornada;

/**
 * Calculate working days for a whole team.
 *
 * This class accepts one or more WorkingDaysCalculator objects, with the idea
 * being that each represents an individual team member. That way, separate
 * sets of PTO or regional holidays can be accounted as they vary across the
 * whole team.
 */
class TeamCalculator
{
    /**
     * @var \Lullabot\Jornada\WorkingDaysCalculator[]
     */
    private $calculators = [];

    /**
     * Add a calculator to the team.
     *
     * @param string                                  $id         A unique ID to reference the calculator by. To update an existing
     *                                                            calculator, call this method again with the same ID.
     * @param \Lullabot\Jornada\WorkingDaysCalculator $calculator the calculator to add
     */
    public function addCalculator(
        string $id,
        WorkingDaysCalculator $calculator
    ): void {
        $this->calculators[$id] = $calculator;
    }

    /**
     * Return the number of working days for the whole team.
     */
    public function getWorkingDays(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        $days = 0;
        foreach ($this->calculators as $calculator) {
            $days += $calculator->getWorkingDays($startDate, $endDate);
        }

        return $days;
    }

    /**
     * Return the number of business days for the whole team.
     */
    public function getBusinessDays(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        $days = 0;
        foreach ($this->calculators as $calculator) {
            $days += $calculator->getBusinessDays($startDate, $endDate);
        }

        return $days;
    }

    /**
     * Return calculated individual results for the whole team.
     *
     * @return \Lullabot\Jornada\WorkingDaysResult[]
     */
    public function getIndividualResults(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $results = [];
        if ($startDate instanceof \DateTime) {
            $startDate = \DateTimeImmutable::createFromMutable($startDate);
        } else {
            $startDate = clone $startDate;
        }

        foreach ($this->calculators as $id => $calculator) {
            $last = $calculator->getLastDay($startDate, $endDate);
            $days = $calculator->getWorkingDays($startDate, $endDate);
            $businessDays = $calculator->getBusinessDays($startDate, $endDate);
            $results[] = new WorkingDaysResult($id, $startDate, $last, $days, $businessDays);
        }

        return $results;
    }
}
