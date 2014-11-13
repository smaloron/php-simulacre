<?php
    namespace Simulacre;


    class Simulacre
    {

        /**
         * @var \Simulacre\SimulacreFactory
         */
        public $Factory;

        /**
         * @var array
         */
        private static $config = [];
        /**
         * @var array
         */
        private $tables;

        public function __construct($userConfig = []) {
            $defaultConfig = include dirname(__FILE__) . '/../../config.php';
            $config        = array_merge($defaultConfig, $userConfig);

            $this->parseConfig($config);

            self::$config = $config;

        }

        public function getFactory() {
            return $this->Factory;
        }

        private function parseConfig($config) {

            $this->Factory = new SimulacreFactory($config['classMaps']);
            $tables        = $config['tables'];

            foreach ($tables as $key => $value) {
                $this->addTable($key, $value);
            }
        }

        /**
         * @param string $tableKey
         * @param array  $config
         */
        private function addTable($tableKey, array $config) {
            $tableInstance = new SimulacreTable($config, $this->Factory);
            foreach ($config['fields'] as $key => $config) {
                $tableInstance->addField($key, $config);
            }
            $this->tables [$tableKey] = $tableInstance;
        }

        public function runProject() {
            foreach ($this->tables as $tableInstance) {
                $outputInstance = $this->Factory->createOutput($tableInstance);
                $outputInstance->run();

            }
        }

        public static function getDataSources($key) {
            $dataSources = self::$config['dataSources'];
            if (!array_key_exists($key, $dataSources)) {
                throw new \Exception ("The key " . $key . " does not exists in the datasources collection");
            }

            return $dataSources[$key];
        }

        public static function getOutputPath() {
            return self::$config['outputPath'];
        }

    }