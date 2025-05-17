<?php

if (!function_exists('money')) {
    /**
     * Format a number as currency
     *
     * @param float|int|string $amount
     * @param string $currency
     * @return string
     */
    function money($amount, $currency = 'TSH') {        return number_format((float) $amount, 2) . ' ' . $currency;    }
}

if (!function_exists('percentage')) {
    /**
     * Format a number as percentage
     *
     * @param float|int|string $value
     * @param int $decimals
     * @return string
     */
    function percentage($value, $decimals = 2) {
        return number_format((float) $value, $decimals) . '%';
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date
     *
     * @param string|DateTime $date
     * @param string $format
     * @return string
     */
    function format_date($date, $format = 'd M Y') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate HTML for a status badge
     *
     * @param string $status
     * @return string
     */
    function status_badge($status) {
        $classes = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'active' => 'bg-success',
            'inactive' => 'bg-danger',
        ];

        $class = $classes[strtolower($status)] ?? 'bg-secondary';
        return '<span class="badge ' . $class . '">' . ucfirst($status) . '</span>';
    }
} 