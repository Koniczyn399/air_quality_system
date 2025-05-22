<?php

return [
    'attributes' => [

        'id' => 'ID',
        'name' => 'Imię i Nazwisko',
        'phone_number' => 'Nr telefonu',
        'last_name' => 'Nazwisko',

        'email' => 'Adres e-mail',

        'roles' => 'Role',
        'password' => 'Hasło',
    ],
    'actions' => [
        'create' => 'Dodaj nowego użytkownika',

        'edit_user' => 'Zmodyfikuj użytkownika',
        'remove_user' => 'Usuń użytkownika',
    ],

    'labels' => [
        'create_form_title' => 'Dodawanie nowego użytkownika',
        'edit_form_title' => 'Edycja użytkownika',
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
