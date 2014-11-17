<?php
    namespace Simulacre\Output;

    class SqlFileOutput extends BaseFileOutput
    {

        protected $_fileExtension = 'sql';
        protected $_fieldSeparator = ',';
        protected $_valueSeparator = "'";


        protected function setContentHeader() {
            $fieldsNames = $this->_tableInstance->getOutputtedFieldsNames();
            $sql         = "INSERT INTO" . " " . $this->_tableInstance->getTableName() . " (" . implode(',', $fieldsNames) . ") VALUES \n";

            return $sql;
        }

        protected function formatFields($record) {
            return implode($this->_fieldSeparator, $record);
        }

        protected function writeRecordsToFile($header, $records, $file) {
            $content = $header . '(' . implode("),\n(", $records) . ")";
            $success = file_put_contents($file . $this->_fileExtension, $content);
            if (!$success) {
                throw new \Exception ("The  file " . $file . $this->_fileExtension . " could not be written");
            }
        }

        protected function formatValue($value) {
            if ($value == null || $value == 'null') {
                return 'null';
            } else {
                return $this->_valueSeparator . addslashes($value) . $this->_valueSeparator;
            }
        }


    }