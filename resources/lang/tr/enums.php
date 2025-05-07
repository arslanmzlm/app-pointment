<?php

return [
    'default' => 'Belirtilmemiş',
    'gender' => [
        'MALE' => 'Erkek',
        'FEMALE' => 'Kadın',
    ],
    'field' => [
        'input' => [
            'TEXT' => 'Yazı',
            'NUMBER' => 'Sayı',
            'DATE' => 'Tarih',
            'SELECT' => 'Seçenek Kutusu',
            'RADIO' => 'Seçenekler',
            'CHECKBOX' => 'Çoklu Seçenek',
            'RADIO_TEXT' => 'Seçenekler ve Yazı',
        ]
    ],
    'user' => [
        'type' => [
            'PATIENT' => 'Hasta',
            'DOCTOR' => 'Podolog',
            'ADMIN' => 'Yönetici',
        ],
    ],
    'transaction' => [
        'type' => [
            'INCOME' => 'Gelir',
            'EXPENSE' => 'Gider',
        ],
    ],
    'payment' => [
        'method' => [
            'CASH' => 'Nakit',
            'CARD' => 'Banka/Kredi Kartı',
            'TRANSFER' => 'Havale/EFT',
        ],
    ],
    'appointment' => [
        'state' => [
            'PENDING' => 'Onay bekliyor',
            'CONFIRMED' => 'Onaylandı',
            'COMPLETED' => 'Tamamlandı',
            'RESCHEDULED' => 'Ertelendi',
            'CANCELED' => 'İptal edildi',
        ],
    ],
    'message' => [
        'type' => [
            'APPOINT' => 'Yeni Randevu',
            'REMIND' => 'Hatırlatma',
        ],
    ],
];
