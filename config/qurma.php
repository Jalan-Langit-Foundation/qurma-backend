<?php

return [
    'program_year'   => 2027,
    'default_tenor'  => 7,
    'deadline_month' => '2027-04', // SEMENTARA, diganti pengaturan admin di langkah B
    'rounding'       => 5000,

    // Harga final dari Kang Abay (17 Nov 2026). Bobot dari draft Brief Revisi V0.2 (belum dikonfirmasi).
    'packages' => [
        'standar' => ['name' => 'Domba Standar', 'price' => 1600000, 'weight_min' => 20, 'weight_max' => 25],
        'medium'  => ['name' => 'Domba Medium',  'price' => 2200000, 'weight_min' => 26, 'weight_max' => 29],
        'premium' => ['name' => 'Domba Premium', 'price' => 2990000, 'weight_min' => 30, 'weight_max' => 35],
    ],
];