<?php
    namespace Simulacre\Output;

    use Simulacre\SimulacreBaseClass;
    use Simulacre\SimulacreTable;

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
        /**
         * @var int
         */
        protected $_maxNumberOfLinesPerFiles = 10000;

        function __construct($params = []) {
            parent::hydrate($this,$params);
            if($this->_fileExtension[0] != '.'){
                $this->_fileExtension = '.'.$this->_fileExtension;
            }
        }


        /**
         * @param \Simulacre\SimulacreTable $tableInstance
         */
        public function setTableInstance(SimulacreTable $tableInstance) {
            $this->_tableInstance = $tableInstance;
        }

        public abstract function run();

        /**
         *
         *
         * @param $header
         * @param $records
         * @param $file
         *
         * @throws \Exception
         */
        protected function writeRecordsToFile($header,$records, $file){
            $content = $header.$this->_recordSeparator. implode($this->_recordSeparator,$records);
            $success = file_put_contents($file.$this->_fileExtension,$content);
            if(! $success){
                throw new \Exception ("Le fichier " . $file.$this->_fileExtension. " n'a pu être créé" );
            }
        }

        protected function getValuesFromGenerators($iteration) {
            $fields = $this->_tableInstance->getOutputtedFields();
            $values = [];

            foreach ($fields as $generator) {
                array_push($values, $generator->getFormattedValue($iteration));
            }

            return $values;
        }

        protected function formatValue($value) {
            if ($value == null || $value == 'null') {
                return 'null';
            } else {
                return '"' . $value . '"';
            }
        }

    }