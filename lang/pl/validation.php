<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    // 'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
    'accepted_if' => 'Pole :attribute musi zostać zaakceptowane jeśli pole :other ma wartość :value.',
    'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
    'after' => 'Pole :attribute musi być datą późniejszą od :date.',
    'after_or_equal' => 'Pole :attribute musi być datą nie wcześniejszą niż :date.',
    'alpha' => 'Pole :attribute może zawierać jedynie litery.',
    'alpha_dash' => 'Pole :attribute może zawierać jedynie litery, cyfry i myślniki.',
    'alpha_num' => 'Pole :attribute może zawierać jedynie litery i cyfry.',
    'array' => 'Pole :attribute musi być tablicą.',
    'before' => 'Pole :attribute musi być datą wcześniejszą od :date.',
    'before_or_equal' => 'Pole :attribute musi być datą nie późniejszą niż :date.',
    'between' => [
        'numeric' => 'Pole :attribute musi zawierać się w granicach :min - :max.',
        'file' => 'Pole :attribute musi zawierać się w granicach :min - :max kilobajtów.',
        'string' => 'Pole :attribute musi zawierać się w granicach :min - :max znaków.',
        'array' => 'Pole :attribute musi składać się z :min - :max elementów.',
    ],
    'boolean' => 'Pole :attribute musi mieć wartość logiczną prawda albo fałsz.',
    'confirmed' => 'Potwierdzenie pola :attribute nie zgadza się.',
    'current_password' => 'Hasło jest niepoprawne.',
    'date' => 'Pole :attribute nie jest prawidłową datą.',
    'date_equals' => 'Pole :attribute musi być datą równą :date.',
    'date_format' => 'Pole :attribute nie jest w formacie :format.',
    'declined' => 'Pole :attribute musi być wyłączone.',
    'declined_if' => 'Pole :attribute musi być wyłączone jeśłi pole :other ma wartość :value.',
    'different' => 'Pole :attribute oraz :other muszą się różnić.',
    'digits' => 'Pole :attribute musi składać się z :digits cyfr.',
    'digits_between' => 'Pole :attribute musi mieć od :min do :max cyfr.',
    'dimensions' => 'Pole :attribute ma niepoprawne wymiary.',
    'distinct' => 'Pole :attribute ma zduplikowane wartości.',
    'doesnt_start_with' => 'Pole :attribute nie rozpoczyna się od jednej z następujących wartości: :values.',
    'email' => 'Format pola :attribute jest nieprawidłowy.',
    'ends_with' => 'Pole :attribute musi kończyć się jedną z następujących wartości: :values.',
    'enum' => 'Wybrana wartość pola :attribute jest niepoprawna.',
    'exists' => 'Zaznaczony :attribute jest nieprawidłowy.',
    'file' => 'Pole :attribute musi być plikiem.',
    'filled' => 'Pole :attribute jest wymagane.',
    'gt' => [
        'numeric' => 'Pole :attribute musi być większe niż :value.',
        'file' => 'Pole :attribute musi być większe niż :value kilobajtów.',
        'string' => 'Pole :attribute musi być dłuższe niż :value znaków.',
        'array' => 'Pole :attribute musi mieć więcej niż :value elementów.',
    ],
    'gte' => [
        'numeric' => 'Pole :attribute musi być większe lub równe :value.',
        'file' => 'Pole :attribute musi być większe lub równe :value kilobajtów.',
        'string' => 'Pole :attribute musi być dłuższe lub równe :value znaków.',
        'array' => 'Pole :attribute musi mieć :value lub więcej elementów.',
    ],
    'image' => 'Pole :attribute musi być obrazkiem.',
    'in' => 'Zaznaczony element :attribute jest nieprawidłowy.',
    'in_array' => 'Pole :attribute nie znajduje się w :other.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.',
    'ip' => 'Pole :attribute musi być prawidłowym adresem IP.',
    'ipv4' => 'Pole :attribute musi być prawidłowym adresem IPv4.',
    'ipv6' => 'Pole :attribute musi być prawidłowym adresem IPv6.',
    'json' => 'Pole :attribute musi być poprawnym ciągiem znaków JSON.',
    'lt' => [
        'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
        'file' => 'Pole :attribute musi być mniejsze niż :value kilobajtów.',
        'string' => 'Pole :attribute musi być krótsze niż :value znaków.',
        'array' => 'Pole :attribute musi mieć mniej niż :value elementów.',
    ],
    'lte' => [
        'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
        'file' => 'Pole :attribute musi być mniejsze lub równe :value kilobajtów.',
        'string' => 'Pole :attribute musi być krótsze lub równe :value znaków.',
        'array' => 'Pole :attribute musi mieć :value lub mniej elementów.',
    ],
    'mac_address' => 'Pole :attribute musi być poprawny adresem MAC.',
    'max' => [
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'file' => 'Pole :attribute nie może być większe niż :max kilobajtów.',
        'string' => 'Pole :attribute nie może być dłuższe niż :max znaków.',
        'array' => 'Pole :attribute nie może mieć więcej niż :max elementów.',
    ],
    'mimes' => 'Pole :attribute musi być plikiem typu :values.',
    'mimetypes' => 'Pole :attribute musi być plikiem typu :values.',
    'min' => [
        'numeric' => 'Pole :attribute musi być nie mniejsze od :min.',
        'file' => 'Pole :attribute musi mieć przynajmniej :min kilobajtów.',
        'string' => 'Pole :attribute musi mieć przynajmniej :min znaków.',
        'array' => 'Pole :attribute musi mieć przynajmniej :min elementów.',
    ],
    'multiple_of' => 'Pole :attribute musi być wielokrotnością wartości :value.',
    'not_in' => 'Zaznaczony :attribute jest nieprawidłowy.',
    'not_regex' => 'Format pola :attribute jest nieprawidłowy.',
    'numeric' => 'Pole :attribute musi być liczbą.',
    'password' => [
        'letters' => 'Pole :attribute mus zawierać co najmnije jedną literę.',
        'mixed' => 'Pole :attribute musi zawierać co najmnije jedną małą i jedną duż literę.',
        'numbers' => 'Pole :attribute musi zawierać co najmnije jedą cyfrę.',
        'symbols' => 'Pole :attribute mis zawierać co najmnije jeden znak specjalny.',
        'uncompromised' => 'Wartośc podana w polu :attribute has appeared in a data leak. Please choose a different :attribute.',
        'nomatch' => 'Podane hasło nie pasuje do aktualnego hasła.',
    ],
    'present' => 'Pole :attribute musi być obecne.',
    'prohibited' => 'Pole :attribute jest zabronione.',
    'prohibited_if' => 'Pole :attribute jest zabronione, gdy :other to :value.',
    'prohibited_unless' => 'Pole :attribute jest zabronione, chyba że :other jest w :values.',
    'prohibits' => 'Pole :attribute zabrania obecności :other.',
    'regex' => 'Format pola :attribute jest nieprawidłowy.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_array_keys' => 'Pole :attribute musi zawierać wartości: :values.',
    'required_if' => 'Pole :attribute jest wymagane gdy :other ma wartość :value.',
    'required_unless' => 'Pole :attribute jest wymagane jeżeli :other nie znajduje się w :values.',
    'required_with' => 'Pole :attribute jest wymagane gdy :values jest obecny.',
    'required_with_all' => 'Pole :attribute jest wymagane gdy wszystkie :values są obecne.',
    'required_without' => 'Pole :attribute jest wymagane gdy :values nie jest obecny.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z :values nie są obecne.',
    'same' => 'Pole :attribute i :other muszą być takie same.',
    'size' => [
        'numeric' => 'Pole :attribute musi mieć :size.',
        'file' => 'Pole :attribute musi mieć :size kilobajtów.',
        'string' => 'Pole :attribute musi mieć :size znaków.',
        'array' => 'Pole :attribute musi zawierać :size elementów.',
    ],
    'starts_with' => 'Pole :attribute musi się zaczynać jednym z wymienionych: :values.',
    'string' => 'Pole :attribute musi być ciągiem znaków.',
    'timezone' => 'Pole :attribute musi być prawidłową strefą czasową.',
    'unique' => 'Pole :attribute musi być unikalne.',
    'uploaded' => 'Nie udało się wgrać pliku :attribute.',
    'url' => 'Format pola :attribute jest nieprawidłowy.',
    'uuid' => 'Pole :attribute musi być poprawnym identyfikatorem UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
