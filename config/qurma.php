<?php

return [
    'program_year'       => 2027,
    'default_tenor'      => 7,
    'rounding'           => 5000,

    // Perkiraan tanggal Idul Adha 2027, dipakai selama admin belum mengisi tanggal resmi
    'idul_adha_estimate' => '2027-05-17',

    // Harga final dari Kang Abay (17 Nov 2026). Bobot dari draft Brief Revisi (belum dikonfirmasi).
    'packages' => [
        'standar' => ['name' => 'Domba Standar', 'price' => 1600000, 'weight_min' => 20, 'weight_max' => 25],
        'medium'  => ['name' => 'Domba Medium',  'price' => 2200000, 'weight_min' => 26, 'weight_max' => 29],
        'premium' => ['name' => 'Domba Premium', 'price' => 2990000, 'weight_min' => 30, 'weight_max' => 35],
    ],
];