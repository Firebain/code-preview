<?php

return [
    'test_mode' => env("ROBOKASSA_TEST_MODE", false),
    'login' => env("ROBOKASSA_LOGIN", 'em2'),
    'passwords' => [
        'first' => env("ROBOKASSA_FIRST_PASSWORD", "robokassa"),
        'second' => env("ROBOKASSA_SECOND_PASSWORD", "robokassa")
    ]
];