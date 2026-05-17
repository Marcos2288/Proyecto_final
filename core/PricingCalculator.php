<?php

class PricingCalculator
{
    public const PLAN_BASIC = 'basico';
    public const PLAN_PREMIUM = 'premium';
    private const PREMIUM_DISCOUNT_RATE = 0.05;

    public static function getUserPlan(array|null $user = null): string
    {
        if (is_array($user) && !empty($user['tipo_plan'])) {
            $plan = strtolower($user['tipo_plan']);
            return in_array($plan, [self::PLAN_BASIC, self::PLAN_PREMIUM], true) ? $plan : self::PLAN_BASIC;
        }

        if (!empty($_SESSION['tipo_plan'])) {
            $plan = strtolower($_SESSION['tipo_plan']);
            return in_array($plan, [self::PLAN_BASIC, self::PLAN_PREMIUM], true) ? $plan : self::PLAN_BASIC;
        }

        return self::PLAN_BASIC;
    }

    public static function isPremiumUser(array|null $user = null): bool
    {
        return self::getUserPlan($user) === self::PLAN_PREMIUM;
    }

    public static function calculatePrice(float $price, array|null $user = null): float
    {
        if (self::isPremiumUser($user)) {
            return round($price * (1 - self::PREMIUM_DISCOUNT_RATE), 2);
        }
        return round($price, 2);
    }

    public static function calculateDiscount(float $price, int $quantity = 1, array|null $user = null): float
    {
        if (!self::isPremiumUser($user)) {
            return 0.0;
        }
        return round($price * $quantity * self::PREMIUM_DISCOUNT_RATE, 2);
    }

    public static function formatPriceWithDiscount(float $price, array|null $user = null): string
    {
        $final = self::calculatePrice($price, $user);
        $formattedFinal = Formatter::formatPrice($final);

        if (!self::isPremiumUser($user)) {
            return '<span class="price-current">' . Formatter::escape($formattedFinal) . '</span>';
        }

        return '<span class="price-old">' . Formatter::escape(Formatter::formatPrice($price)) . '</span>'
            . '<span class="price-current">' . Formatter::escape($formattedFinal) . '</span>'
            . '<span class="price-discount">Premium -5%</span>';
    }

    public static function calculateCartTotal(array $items, array|null $user = null): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += self::calculatePrice((float) ($item['precio'] ?? 0), $user) * ((int) ($item['cantidad'] ?? 1));
        }
        return round($total, 2);
    }

    public static function calculateCartDiscount(float $subtotal, array|null $user = null): float
    {
        if (!self::isPremiumUser($user)) {
            return 0.0;
        }
        return round($subtotal * self::PREMIUM_DISCOUNT_RATE, 2);
    }
}
