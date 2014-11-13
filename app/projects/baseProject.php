<?php

    $contact = [
        'tableName'       => 'pers',
        'outputMethod'    => 'csv',
        'numberOfRecords' => 1000,
        'outputParams'    => ['outputMethod' => 'csv'],
        'fields'          => [
            'name'          => [
                'generator' => 'name'
            ],
            'first_name'    => [
                'generator' => 'firstName'
            ],
            'age'           => [
                'generator' => 'integer',
                'params'    => ['minValue' => 5, 'maxValue' => 99]
            ],
            'date_creation' => [
                'generator' => 'date',
                'startDate' => '2000-01-01',
                'endDate'   => '2014-10-20'
            ],
        ]
    ];
    $config  = [
        'tables' => [$contact]
    ];

    return $config;