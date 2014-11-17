<?php

    $generatorClassMap = [
        'constant'      => ['className' => '\Simulacre\Generators\ConstantGenerator'],
        'integer'       => ['className' => '\Simulacre\Generators\IntegerGenerator'],
        'real'          => ['className' => '\Simulacre\Generators\RealGenerator',],
        'weighted'      => ['className' => '\Simulacre\Generators\WeightedRangeGenerator',],
        'date'          => ['className' => '\Simulacre\Generators\DateGenerator'],
        'databaseQuery' => ['className' => '\Simulacre\Generators\FromDataBaseGenerator'],
        'lookUp'        => ['className' => '\Simulacre\Generators\LookUpGenerator'],
        'foreignKey'    => ['className' => '\Simulacre\Generators\ForeignKeyGenerator'],
        'textFile'      => ['className' => '\Simulacre\Generators\FromTextFileGenerator'],

        'name'          => ['className' => '\Simulacre\Generators\FromDataBaseGenerator',
                            'params'    => ['generator'  => 'databaseQuery',
                                            'sqlString'  => 'SELECT name FROM names',
                                            'dataSource' => 'default']
        ],

        'firstName'     => ['className' => '\Simulacre\Generators\FromDataBaseGenerator',
                            'params'    => ['generator'  => 'databaseQuery',
                                            'sqlString'  => 'SELECT name, sex FROM firstNames',
                                            'dataSource' => 'default']
        ]
    ];

    $dataSources = [
        'default' => new \PDO(
            'sqlite:' . dirname(__FILE__) . '/data/simulacre.sqlite',
            null,
            null,
            array(\PDO::ERRMODE_EXCEPTION => true)
        )
    ];

    $outputClassmap = [
        'csv'      => ['className' => '\Simulacre\Output\CsvFileOutput'],
        'sql'      => ['className' => '\Simulacre\Output\SqlFileOutput'],
        'database' => ['className' => '\Simulacre\Output\DatabaseInsertOutput']

    ];

    $classMap = [
        'generatorFactory' => '\Simulacre\GeneratorFactory'
    ];

    $config = [
        'basePath'        => dirname(__FILE__),
        'outputPath'      => dirname(dirname(__FILE__)) . '/outputFiles',
        'defaultDataPath' => dirname(__FILE__) . '/data/',
        'classMaps'       => [
            'generators' => $generatorClassMap,
            'outputs'    => $outputClassmap,
            'generics'   => $classMap,
        ],
        'dataSources'     => $dataSources,

    ];


    return $config;