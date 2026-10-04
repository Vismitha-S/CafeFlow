<?php

return [
    // Default reservation duration in minutes
    'default_duration_minutes' => (int) env('RESERVATION_DEFAULT_DURATION_MINUTES', 90),

    // Sensible limits for guest counts
    'min_guests' => 1,
    'max_guests' => (int) env('RESERVATION_MAX_GUESTS', 20),

    // Status definitions
    'statuses' => [
        'pending' => 'pending',
        'confirmed' => 'confirmed',
        'cancelled' => 'cancelled',
        'completed' => 'completed',
        'no_show' => 'no_show',
    ],

    // Statuses that block table availability
    'blocking_statuses' => [
        'pending',
        'confirmed',
    ],

    // Statuses that do not block table availability
    'non_blocking_statuses' => [
        'cancelled',
        'completed',
        'no_show',
    ],
];
