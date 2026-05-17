<?php
require_once __DIR__ . '/bootstrap.php';

function e($value) {
    return Formatter::escape((string) $value);
}

function excerpt($value, $length = 95) {
    return Formatter::excerpt((string) $value, $length);
}

function user_plan($user = null) {
    return PricingCalculator::getUserPlan(is_array($user) ? $user : null);
}

function plan_label($plan) {
    return PricingCalculator::isPremiumUser(['tipo_plan' => $plan]) ? 'Premium' : 'Basico';
}

function is_premium_user($user = null) {
    return PricingCalculator::isPremiumUser(is_array($user) ? $user : null);
}

function premium_discount_rate() {
    return 0.05;
}

function product_price($price, $user = null) {
    return PricingCalculator::calculatePrice((float) $price, is_array($user) ? $user : null);
}

function premium_discount_amount($price, $quantity = 1, $user = null) {
    return PricingCalculator::calculateDiscount((float) $price, (int) $quantity, is_array($user) ? $user : null);
}

function product_price_html($price, $user = null) {
    return PricingCalculator::formatPriceWithDiscount((float) $price, is_array($user) ? $user : null);
}
