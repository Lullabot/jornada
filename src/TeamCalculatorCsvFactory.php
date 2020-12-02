<?php

declare(strict_types=1);

namespace Lullabot\Jornada;

/**
 * Create a TeamCalculator from a set of CSVs.
 *
 * This class uses two CSVs. The "Booked PTO" CSV represents upcoming PTO that
 * has been booked by a team member. The CSV must have two columns:
 *
 *   person,day
 *   Andrew Berry,2020-04-11
 *
 * "day" is an ISO date, such as 2020-04-11 for April 11th 2020.
 *
 * The "Owed PTO" CSV represents the amounts and types of upcoming time off for
 * a given member. The "type" can be any value that makes sense for your
 * business.
 *
 *   person,type,days
 *   Andrew Berry,Statutory Holiday,3
 */
class TeamCalculatorCsvFactory
{
    /**
     * Return a TeamCalculator based on booked and owed PTO.
     *
     * @param \SplFileObject $bookedPto a reference to the CSV with booked PTO
     * @param \SplFileObject $owedPto   a reference to the CSV with owed PTO
     *
     * @return \Lullabot\Jornada\TeamCalculator
     */
    public function fromCsv(\SplFileObject $people = null, \SplFileObject $bookedPto = null, \SplFileObject $owedPto = null): TeamCalculator
    {
        $calculators = [];

        if ($people) {
            $people->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
            while ($person = $people->getCurrentLine()) {
                $calculators[$person] = new WorkingDaysCalculator();
            }
        }

        if ($bookedPto) {
            $line = 0;
            $bookedPto->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
            while ($data = $bookedPto->fgetcsv()) {
                if ($line > 0) {
                    $person = $data[0];
                    $date = \DateTimeImmutable::createFromFormat('Y-m-d', $data[1]);
                    if (!isset($calculators[$person])) {
                        $calculators[$person] = new WorkingDaysCalculator();
                    }
                    $calculators[$person]->addHoliday($date);
                }
                ++$line;
            }
        }

        if ($owedPto) {
            $line = 0;
            $owedPto->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
            while ($data = $owedPto->fgetcsv()) {
                if ($line > 0) {
                    $person = $data[0];
                    $days = (int) $data[2];
                    if (!isset($calculators[$person])) {
                        $calculators[$person] = new WorkingDaysCalculator();
                    }
                    $calculators[$person]->addUnbookedHolidayDays($days);
                }
                ++$line;
            }
        }

        $team = new TeamCalculator();
        foreach ($calculators as $id => $calculator) {
            $team->addCalculator($id, $calculator);
        }

        return $team;
    }
}
