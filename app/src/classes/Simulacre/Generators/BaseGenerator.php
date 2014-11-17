<?php

    namespace Simulacre\Generators;

    use Simulacre\SimulacreBaseClass;

    abstract class  BaseGenerator extends SimulacreBaseClass
    {

        /**
         * @var array list of closures to apply formatting logic to the generated value
         */
        protected $_formatters = array();

        /**
         * @var int current iteration number
         */
        protected $_iteration = 0;

        protected $_generatedValue;
        protected $_formattedValue;
        protected $_percentOfNull = 0;

        /**
         * @var bool specifies whether the generator value should be included in the output
         *           Some generator value can be used by other generators and should not map to a database field
         */
        protected $_includedInOutput = true;

        protected $_validator;
        /**
         * @var \Simulacre\SimulacreTable
         */
        protected $_tableInstance;

        public function setValidator($validatorInstance) {
            $this->_validator = $validatorInstance;
        }

        public function setTableInstance(\Simulacre\SimulacreTable $tableInstance) {
            $this->_tableInstance = $tableInstance;
        }

        /**
         * This method provides the actual code which generate the value
         * It must be overridden by the children class extending BaseGenerator
         * @return mixed
         */
        protected abstract function calculateValue();

        /**
         * If a percent of null is specified, determine whether the value should be null or calculated.
         */
        protected function generateValue() {
            $this->_iteration++;
            if ($this->_percentOfNull > 0 && mt_rand(1, 100) < $this->_percentOfNull) {
                $value = 'null';
            } else {
                $value = $this->calculateValue();
            }
            $this->_generatedValue = $value;
        }

        /**
         * Generate a new value or returns the previously generated value for this iteration
         *
         * @param int $iteration
         *
         * @return mixed
         */
        public function getValue($iteration = 0) {
            if ($this->_generatedValue == null || $iteration = 0 || $iteration != $this->_iteration) {
                $this->generateValue();
            }

            return $this->_generatedValue;
        }

        /**
         * Returns the generated value formatted by the closures defined in the formatters array
         *
         * @param int $iteration
         *
         * @return mixed
         */
        public function getFormattedValue($iteration = 0) {
            $value         = $this->getValue($iteration);
            $formatedValue = $this->applyFormaters($value);

            return $formatedValue;
        }

        /**
         * Apply all the closures in the formatters array to the generated value
         * @return mixed
         */
        private function applyFormaters() {
            $value = $this->_generatedValue;
            for ($i = 0; $i < count($this->_formatters); $i++) {
                if (is_callable($this->_formatters[$i])) {
                    $value = $this->_formatters[$i]($value, $this);
                }
            }

            return $value;
        }

        public function getIteration() {
            return $this->_iteration;
        }

        /**
         * Lookup a value in a previously defined generator
         *
         * @param $generatorName String
         * @param $key           String
         *
         * @return mixed
         */
        public function getLookUpValue($generatorName, $key = null) {
            $generator = $this->getForeignGenerator($generatorName);
            $value     = $generator->getValue($this->_iteration);
            if (isset($key) && method_exists($generator, 'lookUpFromArray')) {
                $value = $generator->lookUpFromArray($key, $this->_iteration);
            }

            return $value;
        }

        /**
         * get the instance of another field's generator
         * this methods is used for look up services,
         * but it can also be called by anonymous functions (formatters or stopping functions)
         *
         * @param $generatorFieldName String
         *
         * @return \Simulacre\Generators\BaseGenerator
         */
        public function getForeignGenerator($generatorFieldName) {
            return $this->_tableInstance->getFieldByName($generatorFieldName);
        }

        /**Whether the generator value should be present in the output
         * @return bool
         */
        public function isToBeOutputted() {
            return $this->_includedInOutput;
        }


    }