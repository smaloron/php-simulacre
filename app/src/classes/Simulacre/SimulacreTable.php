<?php

    namespace Simulacre;

    /**
     * This class represents a Simulacre table
     * Such a table contains a collection of generators representing the fields
     *
     * Class SimulacreTable
     * @package Simulacre
     */

    class SimulacreTable extends SimulacreBaseClass
    {
        /**
         * @var string name of the table to be used with the sql output
         *             for a csv output, it will be the name of the outputted file
         */
        protected $_tableName;
        /**
         * @var int the number of records to be generated
         */
        protected $_numberOfRecords = 10;
        /**
         * @var mixed references a closure that returns true when the process is to be terminated
         */
        protected $_stoppingRule;

        /**
         * @var int the current iteration
         */
        private $_iteration = 0;
        /**
         * @var array Associative array representing the fields of the table and their generators
         *            the keys represents the fields name that will be used for the output
         */
        protected $_fields = [];

        protected $_outputParams = ['outputMethod' => 'csv'];

        /**
         * @var bool
         */
        private $stopOutputProcess = false;

        /**
         * @var \Simulacre\SimulacreFactory
         */
        private $_simulacreFactory;

        public function __construct($params, \Simulacre\SimulacreFactory $factory) {
            $this->hydrate($this, $params);
            $this->setDefaultStoppingRule();
            $this->_simulacreFactory = $factory;
        }

        public function getTableName() {
            return $this->_tableName;
        }

        /**
         * If no custom stopping rules are defined, the process is terminated
         * when the number of records generated equals the current iteration
         */
        private function setDefaultStoppingRule() {
            if (!isset($this->_stoppingRule)) {
                $this->_stoppingRule = function () {
                    return $this->getNumberOfRecordsLeft() <= 0;
                };
            }
        }

        /**
         * @return callable
         */
        private function getStoppingRule() {
            return $this->_stoppingRule;
        }

        /**
         * This methods tests if the output process must be stopped
         * There are two cases that will stops the process:
         *  1. A generator has sent a message to stop the process
         *  2. A stopping rule function is evaluated to true
         *
         * @return bool
         */
        public function mustStopProcess() {
            $stop = $this->stopOutputProcess;
            if (!$stop) {
                $stoppingRule = $this->getStoppingRule();
                $stop         = $stoppingRule($this);
            }

            return $stop;
        }

        /**
         * This method is used to stop the process, it is called from a generator
         */
        public function stopProcess() {
            $this->stopOutputProcess = true;
        }

        public function getNumberOfRecordsLeft() {
            return $this->_numberOfRecords - $this->_iteration;
        }

        public function setIteration($iteration) {
            $this->_iteration = $iteration;
        }

        /**
         * Add a new field to the associative array and instanciate the generator
         *
         * @param $fieldName String Name of the field
         * @param $params    Array
         *
         * @throws \Exception
         */
        public function addField($fieldName, $params) {
            $generator                 = $this->_simulacreFactory->createGenerator($params, $this);
            $this->_fields[$fieldName] = $generator;
        }

        /**
         * Look up the value of a generator for a given iteration.
         * If the generator current iteration is different from the parameter
         * then the generator will set itself to the given iteration and generate a new value
         * otherwise, it will returns the previously generated value
         *
         * @param String $generatorFieldName
         * @param Int    $iteration
         *
         * @return mixed
         */
        public function getLookUpValue($generatorFieldName, $iteration) {
            return $this->_fields[$generatorFieldName]->getValue($iteration);
        }

        /**
         * get a generator by it's key
         *
         * @param String $generatorFieldName
         *
         * @return mixed
         */
        public function getFieldByName($generatorFieldName) {
            return $this->_fields[$generatorFieldName];
        }

        /**
         * get all the fields
         * @return array
         */
        public function getFields() {
            return $this->_fields;
        }

        /**
         * @return array
         */
        public function getFieldsNames() {
            return array_keys($this->_fields);
        }

        /**
         * get all the values of all the generators for a given iteration
         *
         * @param $iteration
         *
         * @return array
         */
        public function getValuesAt($iteration) {
            $values = [];
            foreach ($this->_fields as $key => $generator) {
                array_push($values, $generator->getFormatedValue($iteration));
            }

            return $values;
        }

        /**
         * Filter the generators and returns only those that must be presents in the output
         * @return array
         */
        public function getOutputtedFields() {
            $fields = array_filter($this->_fields, function ($value) {
                return $value->isToBeOutputted();
            });

            return $fields;
        }

        /**
         * returns only the field names of the outputted generators
         * @return array
         */
        public function getOutputtedFieldsNames() {
            $fields = $this->getOutputtedFields();

            return array_keys($fields);
        }

        public function getOutputParams() {
            return $this->_outputParams;
        }

    }