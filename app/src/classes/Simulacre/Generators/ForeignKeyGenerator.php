<?php

    namespace Simulacre\Generators;

    /**
     * Get values from an sql query to be used as foreign keys
     * For each foreign key, generate a  fixed or random number of records
     *
     * This generator could be used to get a list of clients id and generate a random number of records for each clients.
     *
     * Class ForeignKeyGenerator
     * @package Simulacre\Generators
     */


    class ForeignKeyGenerator extends \Simulacre\Generators\FromDataBaseGenerator
    {
        protected $numberOfValuesByRecords = null;

        private $minNumberOfValues = null;
        private $maxNumberOfValues = null;
        protected $dataSet;

        private $numberOfRecordsLeft;


        public function __construct($params) {
            $this->hydrate($this, $params);
            $this->setNumberOfRecords();

        }

        /**
         * Set the number of records to be generated for the current foreign key
         */
        private function setNumberOfRecords() {
            $numLeft = 1;
            if (isset($this->numberOfValuesByRecords)) {
                if (preg_match('/to/', $this->numberOfValuesByRecords)) {
                    preg_match_all('/[0-9]{1,}/', $this->numberOfValuesByRecords, $matches);
                    if (count($matches[0]) == 2) {
                        $this->minNumberOfValues = (int)$matches[0][0];
                        $this->maxNumberOfValues = (int)$matches[0][1];
                    }
                    $numLeft = mt_rand($this->minNumberOfValues, $this->maxNumberOfValues);
                } else {
                    $numLeft = filter_var($this->numberOfValuesByRecords, FILTER_SANITIZE_NUMBER_INT);
                    $numLeft = $numLeft == null ? 1 : $numLeft;
                }
            }
            $this->numberOfRecordsLeft = $numLeft;
        }

        /**
         * Calculate the foreign key
         * @return mixed
         */
        public function calculateValue() {
            //Get the dataset
            if (!isset($this->dataSet)) {
                $this->dataSet = $this->getDataset();
            }

            //get the foreign key
            $this->currentRecord = $this->dataSet[0];
            $currentValue        = $this->currentRecord[$this->fieldName];

            //if there are no more records to be generated for this value
            //we must then remove the current foreign key from the dataset
            //and calculate a new number of records for the next foreign key
            if ($this->numberOfRecordsLeft == 0) {
                array_shift($this->dataSet);
                //Reset the number of records left
                $this->setNumberOfRecords();
            } else {
                $this->numberOfRecordsLeft--;
            }

            //If the dataset is empty we send a stopping message to the table
            if (count($this->dataSet) == 0) {
                $this->_tableInstance->stopProcess();
            }

            return $currentValue;

        }

        /**
        private function getNumberOfRecords() {
            if (is_integer($this->numberOfRecordsPerFk)) {
                return $this->numberOfRecordsPerFk;
            }
        }
**/
        public function getNumberOfRecordsLeft() {
            return $this->numberOfRecordsLeft;
        }
    }