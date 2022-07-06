<?php
/**
 * Interface of GoldService.
 */
namespace App\Service;

/**
 * Interface GoldServiceInterface.
 */
interface GoldServiceInterface
{
    /**
     * Calculate average gold price.
     *
     * @see GoldService
     *
     * @param array $arr Array
     *
     * @return float Float return
     */
    public function calculateAvg(array $array): float;

    /**
     * Check if date format is correct.
     *
     * @see GoldService
     *
     * @param string $date Date
     * @param string $format Date format
     *
     * @return bool Boolean return
     */
    public function checkIfCorrectFormat(string $date, string $format): bool;

    /**
     * Check if date range is correct.
     *
     * @see GoldService
     *
     * @param string $date Date
     *
     * @return bool Boolean return
     */
    public function checkIfCorrectDate(string $date): bool;
}