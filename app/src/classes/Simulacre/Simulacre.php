<?php
    namespace Simulacre;
    /**
     * This class is the entry point to the Simulacre application,
     * its goal is to set the configuration, instanciate and hydrate the tables and fields
     * and provide a public method to run the output generator
     *
     * Class Simulacre
     * @package Simulacre
     */

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
        private $_tables;

        private $_running = false;

        /**
         * Get the project configuration file and merge it with the application config
         *
         * @param array $userConfig
         */
        public function __construct($userConfig = []) {
            $defaultConfig = include dirname(__FILE__) . '/../../config.php';
            $config        = array_merge($defaultConfig, $userConfig);
            $this->parseConfig($config);
            self::$config = $config;

        }

        public function getFactory() {
            return $this->Factory;
        }

        /**
         * Parse the project config to create the table instances
         *
         * @param $config
         */
        private function parseConfig($config) {
            $this->Factory = new SimulacreFactory($config['classMaps']);
            $tables        = $config['tables'];
            foreach ($tables as $key => $value) {
                $this->addTable($key, $value);
            }
        }

        /**
         * Instanciate a SimulacreTable class and add the fields.
         *
         * @param string $tableName
         * @param array  $config
         */
        private function addTable($tableName, array $config) {
            $tableInstance = new SimulacreTable($config, $this->Factory);
            foreach ($config['fields'] as $key => $config) {
                $tableInstance->addField($key, $config);
            }
            $this->_tables [$tableName] = $tableInstance;
        }

        /**
         * run the output class for each tables in the project
         */
        public function runProject() {
            $this->_running = true;
            foreach ($this->_tables as $tableInstance) {
                $outputInstance = $this->Factory->createOutput($tableInstance);
                $outputInstance->run();

            }
            $this->_running = false;
        }

        public function isRunning() {
            return $this->_running;
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

        public static function getBasePath() {
            return self::$config['basePath'];
        }

        public static function getConfig(){
            return self::$config;
        }

    }