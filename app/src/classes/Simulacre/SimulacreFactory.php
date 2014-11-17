<?php
    namespace Simulacre;

    /**
     * This class offers some factory methods to create instances of classes
     * referenced in the classmap arrays defined in the config.php file.
     *
     * Class SimulacreFactory
     * @package Simulacre
     */


    class SimulacreFactory
    {

        private $_generatorClassMap;
        private $_outputClassMap;
        private $_genericClassMap;

        public function __construct(array $classMap) {
            $this->_generatorClassMap = $classMap['generators'];
            $this->_outputClassMap    = $classMap['outputs'];
            $this->_genericClassMap   = $classMap['generics'];
        }

        /**
         * Create an instance of a referenced class
         *
         * @param $params   array     : this is the custom parameters array
         * @param $classKey string  : this is the key in the params array that points to the key in the classmap array
         * @param $classMap array   :
         *
         * @return mixed
         * @throws \ErrorException
         */
        private function createInstance($params, $classKey, $classMap) {
            $instanceKey = $params[$classKey];

            if (!array_key_exists($instanceKey, $classMap)) {
                throw new \ErrorException("The key " . $instanceKey . " does not exist in the classmap");
            }
            $classDefinition = $classMap[$instanceKey];
            if (array_key_exists('params', $classDefinition)) {
                $params = array_merge($classDefinition['params'], $params);
            }

            $class    = $classDefinition['className'];
            $instance = new $class ($params);

            return $instance;
        }

        /**
         * Specific factory for generators
         *
         * @param                $params
         * @param SimulacreTable $tableInstance
         *
         * @return mixed
         * @throws \ErrorException
         */
        public function createGenerator($params, SimulacreTable $tableInstance) {
            $instance = $this->createInstance($params, 'generator', $this->_generatorClassMap);
            $instance->setTableInstance($tableInstance);
            //TODO Validator for the parameters
            /*
            $validatorInstance = Core::createInstance('validator');
            $instance->setValidator($validatorInstance);
            */

            return $instance;
        }

        /**
         * Specific factory for output methods
         *
         * @param SimulacreTable $tableInstance
         *
         * @return mixed
         * @throws \ErrorException
         */
        public function createOutput(SimulacreTable $tableInstance) {
            $params   = $tableInstance->getOutputParams();
            $instance = $this->createInstance($params, 'outputMethod', $this->_outputClassMap);
            $instance->setTableInstance($tableInstance);

            return $instance;
        }

        /**
         * Generic factory
         * @param       $key
         * @param array $params
         *
         * @return mixed
         * @throws \ErrorException
         */
        public function create($key, $params = []) {
            $instance = $this->createInstance($params, $key, $this->_genericClassMap);

            return $instance;
        }


    }