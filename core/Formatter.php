<?php

class Formatter
{
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function excerpt(string $value, int $length = 95): string
    {
        $value = trim($value);
        return strlen($value) > $length ? substr($value, 0, $length) . '...' : $value;
    }

    public static function formatPrice(float $amount, string $currency = 'EUR'): string
    {
        return number_format($amount, 2, '.', ',') . ' ' . $currency;
    }

    public static function formatDate(string $datetime, string $format = 'd/m/Y'): string
    {
        $dateTime = new DateTime($datetime);
        return $dateTime->format($format);
    }

    public static function formatRating(float $rating, int $count = 0): string
    {
        return sprintf('%.1f/10 (%d votos)', $rating, $count);
    }

    public static function pluralize(int $count, string $singular, string $plural): string
    {
        return $count === 1 ? $singular : $plural;
    }

    public static function slugToTitle(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
