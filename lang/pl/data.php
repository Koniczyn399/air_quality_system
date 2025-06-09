<?php

return [
    'attributes' => [

        'start_date' => 'Od',
        'end_date' => 'Do',
        'devices' => 'Urządzenia',
        'parameters' => 'Parametry',
    ],
    'actions' => [
        'choose_device' => 'Wybierz urządzenie',
        'all_devices' => 'Wszystkie',
        'generate_system' => 'Generuj raport systemu',
        'generate_devices' => 'Generuj raport wybranych urządzeń',
        'generate_values' => 'Generuj raport pomiarów z danego okresu czasu',


    ],

    'labels' => [
        'create_form_title' => 'Dodawanie nowego pomiaru',
        'edit_form_title' => 'Edycja pomiaru',
        'generate_report' => 'Wygeneruj raport',
        'data_management' =>'Zarządzanie danymi',
    ],

    'messages' => [
        'successes' => [
            'stored' => 'Dodano klienta :name',
            'updated' => 'Zaktualizowano klienta :name',
            'destroyed' => 'Usunięto klienta :name',
            'restored' => 'Przywrócono klienta :name',
        ],
    ],
];
