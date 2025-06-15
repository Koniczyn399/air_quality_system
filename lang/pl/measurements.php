<?php

return [
    'attributes' => [

        'id' => 'ID',
        'measurements_date' => 'Data pomiaru',
        'created_at' => 'Utworzono',
        'device_name' => 'Nazwa urządzenia',

    ],
    'actions' => [
        'create' => 'Dodaj nowego pomiar',
        'show_measurement' => 'Pokaż pomiar',
        'edit_measurement' => 'Zmodyfikuj pomiar',
        'remove_measurement' => 'Usuń pomiar',
    ],

    'labels' => [
        'create_form_title' => 'Dodawanie nowego pomiaru',
        'edit_form_title' => 'Edycja pomiaru',
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
