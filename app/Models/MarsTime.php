<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MarsTime
{
    public const UTC_TIME_FIELD_NAME = 'utcDateTime';

    private ?float $marsSolDate = null;
    private ?float $mtcTime = null;

    private ?string $utcDateTime = null;
    private ?int $utcTimestamp = null;
    private ?int $utcMilliseconds = null;

    private ?float $julianDate = null;
    private ?float $julianDateTerrestrial = null;

    private ?float $daysSinceJ200Epoch = null;

    private array $validationRules = [
        self::UTC_TIME_FIELD_NAME => ['required', 'date']
    ];

    /**
     * MarsTime constructor
     *
     * @param string $utcDateTime Any strtotime valid string
     * @throws ValidationException
     */
    public function __construct(string $utcDateTime)
    {
        Validator::make(['utcDateTime' => $utcDateTime], $this->validationRules)->validate();

        $utcTimestamp = strtotime($utcDateTime);

        $this->utcDateTime = $utcDateTime;
        $this->utcTimestamp = $utcTimestamp;
    }

    /**
     * Returns the Mars Sol Date (MSD)
     *
     * @return float
     */
    public function getMarsSolDate(): float
    {
        if (!$this->marsSolDate) {
            $this->marsSolDate = (($this->getDaysSinceJ200Epoch() - 4.5) / 1.027491252) + 44796.0 - 0.00096;
        }

        return $this->marsSolDate;
    }

    /**
     * Returns the Martian Coordinated Time (MTC) in milliseconds
     *
     * @return float
     */
    public function getMtcTime(): float
    {
        if (!$this->mtcTime) {
            $this->mtcTime = fmod(24 * $this->getMarsSolDate(), 24);
        }

        return $this->mtcTime;
    }

    /**
     * Returns the Martian Coordinated Time (MTC) in the "hh:mm:ss" format
     *
     * @return string
     */
    public function getFormattedMtcTime(): string
    {
        $x = $this->getMtcTime() * 3600;
        $hours = floor($x / 3600);

        if ($hours < 10) {
            $hours = "0" . $hours;
        }

        $y = $x % 3600;
        $minutes = floor($y / 60);

        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }

        $seconds = round($y % 60);

        if ($seconds < 10) {
            $seconds = "0" . $seconds;
        }

        return $hours . ":" . $minutes . ":" . $seconds;
    }

    /**
     * Returns the original UTC Date/Time string used when creating the class
     * All the other calculations in this class are based on this initial value
     *
     * @return string
     */
    public function getUtcDateTime(): string
    {
        return $this->utcDateTime;
    }

    /**
     * Returns the original UTC Date/Time value converted to a UNIX timestamp
     *
     * @return int
     */
    public function getUtcTimestamp(): int
    {
        return $this->utcTimestamp;
    }

    /**
     * Returns the original UTC Date/Time value converted to a UNIX timestamp (in milliseconds)
     *
     * @return int
     */
    public function getUtcMilliseconds(): int
    {
        if (!$this->utcMilliseconds) {
            $this->utcMilliseconds = $this->getUtcTimestamp() * 1000;
        }

        return $this->utcMilliseconds;
    }

    /**
     * Returns the Julian Date based in the original UTC Date/Time passed to this class, in days
     *
     * @return float
     */
    public function getJulianDate(): float
    {
        if (!$this->julianDate) {
            $this->julianDate = 2440587.5 + ($this->getUtcMilliseconds() / 8.64E7);
        }

        return $this->julianDate;
    }

    /**
     * Returns the Julian Terrestrial Time based on the Julian Date, in days
     *
     * @return float
     */
    public function getJulianDateTerrestrial(): float
    {
        if (!$this->julianDateTerrestrial) {
            //TODO: 37 TAI OFFSET - add a parameter for this maybe?
            $this->julianDateTerrestrial = $this->getJulianDate() + ((37 + 32.184) / 86400);
        }

        return $this->julianDateTerrestrial;
    }

    /**
     * Returns the number of (fractional) days since 12:00 on 1 January 2000, in Terrestrial Time
     *
     * @return float
     */
    public function getDaysSinceJ200Epoch(): float
    {
        if (!$this->daysSinceJ200Epoch) {
            $this->daysSinceJ200Epoch = $this->getJulianDateTerrestrial() - 2451545.0;
        }

        return $this->daysSinceJ200Epoch;
    }
}
