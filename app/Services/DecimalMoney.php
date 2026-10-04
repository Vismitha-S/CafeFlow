<?php

namespace App\Services;

use InvalidArgumentException;

final class DecimalMoney
{
    public static function toMinorUnits(string $amount): int
    {
        if (! preg_match('/\A(\d+)(?:\.(\d{1,2}))?\z/', $amount, $matches)) {
            throw new InvalidArgumentException('Amounts must be non-negative decimal strings with at most two decimal places.');
        }

        $wholeUnits = ltrim($matches[1], '0') ?: '0';

        if (strlen($wholeUnits) > 10) {
            throw new InvalidArgumentException('Amount exceeds the supported decimal precision.');
        }

        $fractionalUnits = str_pad($matches[2] ?? '', 2, '0');

        return ((int) $wholeUnits * 100) + (int) $fractionalUnits;
    }

    public static function fromMinorUnits(int $minorUnits): string
    {
        if ($minorUnits < 0) {
            throw new InvalidArgumentException('Amounts cannot be negative.');
        }

        return sprintf('%d.%02d', intdiv($minorUnits, 100), $minorUnits % 100);
    }

    public static function percentageOf(string $amount, string $percentage): string
    {
        $amountInMinorUnits = self::toMinorUnits($amount);
        $percentageInBasisPoints = self::toMinorUnits($percentage);

        if ($percentageInBasisPoints > 10000) {
            throw new InvalidArgumentException('Percentages must be between 0 and 100.');
        }

        $product = $amountInMinorUnits * $percentageInBasisPoints;
        $roundedMinorUnits = intdiv($product + 5000, 10000);

        return self::fromMinorUnits($roundedMinorUnits);
    }
}
