<?php

return [
    'program_year'   => 2027,
    'default_tenor'  => 7,
    'rounding'       => 5000,

    // Harga masih "working architecture" sampai quotation vendor final
    'packages' => [
        'loyalty' => ['name' => 'Loyalty', 'price' => 1500000],
        'start'   => ['name' => 'Start',   'price' => 1900000],
        'plus'    => ['name' => 'Plus',    'price' => 2500000],
        'premium' => ['name' => 'Premium', 'price' => 2990000],
    ],
];