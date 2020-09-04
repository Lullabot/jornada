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

    public function __construct(string $id, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate, int $days)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->days = $days;
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

    public function __toString()
    {
        return sprintf(
            '%s has %s working days remaining, finishing on %s.',
            $this->getId(),
            $this->getDays(),
            $this->getEndDate()->format('Y-m-d')
        );
    }
}
