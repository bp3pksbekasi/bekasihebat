<?php

declare(strict_types=1);

namespace App\Support;

class PhoneNormalizer
{
    public static function normalize(string $phone): string
    {
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        $cleaned = ltrim($cleaned, '+');

        if (str_starts_with($cleaned, '08')) {
            return '628' . substr($cleaned, 2);
        }

        if (str_starts_with($cleaned, '8')) {
            return '62' . $cleaned;
        }

        if (str_starts_with($cleaned, '628')) {
            return $cleaned;
        }

        return $cleaned;
    }

    public static function isValidIndonesian(string $phone): bool
    {
        $normalized = self::normalize($phone);

        return str_starts_with($normalized, '628')
            && strlen($normalized) >= 10
            && strlen($normalized) <= 15;
    }
}
