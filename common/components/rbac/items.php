<?php
return [
    'basic_permission' => [
        'type' => 2,
    ],
    'advanced_permission' => [
        'type' => 2,
    ],
    'guest' => [
        'type' => 1,
        'ruleName' => 'userGroup',
    ],
    'basic' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'basic_permission',
        ],
    ],
    'advanced' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'advanced_permission',
        ],
    ],
];
