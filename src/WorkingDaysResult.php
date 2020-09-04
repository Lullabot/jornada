<?php

declare(strict_types=1);

namespace Lullabot\Jornada;

/**
 * Represents a complete working days result, with a start date, end date, total days, and ID.
 */
class WorkingDaysResult
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $startDate;

    /**
     * @var \DateTimeImmutable
     */
    private $endDate;

    /**
     * @var int
     */
    private $days;

    /**
     * @var int
     */
    private $businessDays;

    public function __construct(string $id, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate, int $days, int $businessDays)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->days = $days;
        $this->businessDays = $businessDays;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function getBusinessDays(): int
    {
        return $this->businessDays;
    }

    public function __toString()
    {
        return sprintf(
            '%s has %s business days remaining, %s working days remaining, finishing on %s.',
            $this->getId(),
            $this->getBusinessDays(),
            $this->getDays(),
            $this->getEndDate()->format('Y-m-d')
        );
    }
}
