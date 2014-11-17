<?php
    namespace Simulacre\Output;

    use Simulacre\Simulacre;
    use Simulacre\SimulacreBaseClass;
    use Simulacre\SimulacreTable;

    /**
     * Generate a text file output for a table instance
     * This abstract class provides the base functionalities for a text output
     * it is extended by the concrete classes for csv or sql string output
     *
     * Class BaseFileOutput
     * @package Simulacre\Output
     */

    abstract class BaseFileOutput extends SimulacreBaseClass
    {
        /**
         * @var int
         */
        protected $_iteration;
        /**
         * @var \Simulacre\SimulacreTable
         */
        protected $_tableInstance;

        protected $_fileExtension = 'csv';
        /**
         * @var string
         */
        protected $_fieldSeparator = ';';
        /**
         * @var string
         */
        protected $_recordSeparator = "\n";

        protected $_valueSeparator = "\"";
        /**
         * @var int
         */
        protected $_maxNumberOfLinesPerFiles = 10000;

        public function __construct($params = []) {
            parent::hydrate($this, $params);
            if ($this->_fileExtension[0] != '.') {
                $this->_fileExtension = '.' . $this->_fileExtension;
            }
            $this->createOutputFolder();
        }

        private function createOutputFolder(){
            $path = Simulacre::getOutputPath() . '/';
            if(! file_exists($path)){
                mkdir($path);
            }
        }


        /**
         * @param \Simulacre\SimulacreTable $tableInstance
         */
        public function setTableInstance(SimulacreTable $tableInstance) {
            $this->_tableInstance = $tableInstance;
        }

        /**
         * run the output process until the table sends a stopping message
         *
         * @throws \Exception
         */
        public function run() {
            //get the header and store it for future reference
            $header    = $this->setContentHeader();
            $iteration = 1;
            $done      = false;
            $records   = [];
            $fileName  = $this->_tableInstance->getTableName();
            $filePath  = Simulacre::getOutputPath() . '/';
            //multiple files can be generated so we need a number to assign unique name to each file
            $fileIteration = 1;

            While (!$done) {
                $this->_tableInstance->setIteration($iteration);

                //Get and format the values of the current record
                $record = $this->getValuesFromGenerators($iteration);
                $record = array_map([$this, 'formatValue'], $record);
                //store the records in an array
                array_push($records, $this->formatFields($record));

                $iteration++;
                $this->_tableInstance->setIteration($iteration);

                //Querying the table instance for the stopping message
                $done = $this->_tableInstance->mustStopProcess();

                //for large number of records, multiple files are generated
                if ($iteration % $this->_maxNumberOfLinesPerFiles == 0) {
                    $fileNumber = str_pad($fileIteration,3,'0',STR_PAD_LEFT);
                    $this->writeRecordsToFile($header, $records, $filePath . $fileName . $fileNumber);
                    $records = [];
                    $fileIteration++;
                }

            }
            //write the remaining records
            if (count($records) > 0) {
                $fileNumber = str_pad($fileIteration,3,'0',STR_PAD_LEFT);
                $this->writeRecordsToFile($header, $records, $filePath . $fileName . $fileNumber);
            }
        }

        /**
         * @return string
         */
        protected abstract function setContentHeader() ;

        /**
         * @param $header
         * @param $records
         * @param $file
         *
         * @throws \Exception
         */
        protected function writeRecordsToFile($header, $records, $file) {
            $content = $header . $this->_recordSeparator . implode($this->_recordSeparator, $records);
            $success = file_put_contents($file . $this->_fileExtension, $content);
            if (!$success) {
                throw new \Exception ("The file " . $file . $this->_fileExtension . " could not be written");
            }
        }

        /**
         * Get all the values for a given iteration.
         * This give us an array that represents a record to be used in the output
         * @param $iteration
         *
         * @return array
         */
        protected function getValuesFromGenerators($iteration) {
            $fields = $this->_tableInstance->getOutputtedFields();
            $values = [];

            foreach ($fields as $generator) {
                array_push($values, $generator->getFormattedValue($iteration));
            }

            return $values;
        }

        /**
         *
         * @param $value
         *
         * @return string
         */
        protected function formatValue($value) {
            if ($value == null || $value == 'null') {
                return 'null';
            } else {
                return $this->_valueSeparator . $value . $this->_valueSeparator;
            }
        }

        protected function formatFields($record) {
            return implode($this->_fieldSeparator, $record);
        }

    }