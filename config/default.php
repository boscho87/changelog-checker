<?php


return [
    'ascendingVersion' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'versionBrackets' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'actions' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'increased' => [
        'check' => false,
        'error' => true,
        'fix' => false,
        //commits allowed without a changelog change
        'fail_after' => 4,
    ],
    'sequence' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'linkChecker' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
];
