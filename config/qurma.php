<?php

return [
    'program_year'  => 2027,
    'default_tenor' => 7,
    'rounding'      => 5000,

    // Sumber: Blueprint Tabungan Qurban QURMA 2027.
    // Harga dan bobot masih "working architecture" sampai quotation vendor final.
    'packages' => [
        'loyalty' => ['name' => 'Loyalty', 'price' => 1500000, 'weight_min' => 20, 'weight_max' => 22, 'loyalty_only' => true],
        'start'   => ['name' => 'Start',   'price' => 1900000, 'weight_min' => 23, 'weight_max' => 25, 'recommended' => true],
        'plus'    => ['name' => 'Plus',    'price' => 2500000, 'weight_min' => 26, 'weight_max' => 28],
        'premium' => ['name' => 'Premium', 'price' => 2990000, 'weight_min' => 29, 'weight_max' => 34],
    ],
];