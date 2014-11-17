<?php
    namespace Simulacre\Generators;

    /**
     * Get  a value from a text file,
     * if the file is delimited, you can specify the column you want to use for the value
     * Class FromTextFileGenerator
     * @package Simulacre\Generators
     */

    use Simulacre\Simulacre;

    class FromTextFileGenerator extends BaseGenerator
    {

        protected $_basePath;
        protected $_fileName;
        protected $_returnedColumnNumber = 0;
        protected $_recordSeparator = "\n";
        protected $_columnSeparator = "\t";
        protected $_currentLine;
        protected $_firstDataLineNumber = 0;

        private $_fileData;
        private $_numberOfRows;
        private $_colsHeaders = [];

        public function __construct($params) {
            $this->hydrate($this, $params);
            if (!isset($this->_basePath)) {
                $this->_basePath = Simulacre::getBasePath() . '/data';
            }

        }

        public function calculateValue() {
            $arrayContent       = $this->getData();
            $lineNumber         = mt_rand($this->_firstDataLineNumber, $this->_numberOfRows - 1);
            $selectedLine       = $arrayContent[$lineNumber];
            $this->_currentLine = explode($this->_columnSeparator, $selectedLine);
            $value              = $this->_currentLine[$this->_returnedColumnNumber];

            return $value;
        }

        private function getData() {
            if (!isset($this->_fileData)) {
                $path                = $this->_basePath . '/' . $this->_fileName;
                $content             = file_get_contents($path);
                $this->_fileData     = explode($this->_recordSeparator, $content);
                $this->_numberOfRows = count($this->_fileData);
                if ($this->_firstDataLineNumber > 0) {
                    $this->_colsHeaders = $this->_fileData[0];
                }
            }

            return $this->_fileData;
        }

        public function lookUpFromArray($key) {
            if (is_string($key)) {
                $colNum = array_search($key, $this->_colsHeaders);
            } else {
                $colNum = $key;
            }

            return $this->_currentLine[$colNum];
        }

    }