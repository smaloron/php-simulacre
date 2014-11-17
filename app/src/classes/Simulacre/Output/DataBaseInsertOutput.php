<?php

namespace Simulacre\Output;


use Simulacre\SimulacreBaseClass;
use Simulacre\SimulacreTable;
use Simulacre\Simulacre;

class DataBaseInsertOutput extends SimulacreBaseClass{

    /**
     * @var int
     */
    protected $_iteration;
    /**
     * @var \Simulacre\SimulacreTable
     */
    protected $_tableInstance;

    protected $_batchSize = 50;

    protected $_dataSource;

    /**
     * @param \Simulacre\SimulacreTable $tableInstance
     */
    public function setTableInstance(SimulacreTable $tableInstance) {
        $this->_tableInstance = $tableInstance;
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    private function getDbConnection(){
        return Simulacre::getDataSources($this->_dataSource);
    }

    private function getSqlInsertString(){
        $sql = "INSERT INTO" . " " . $this->getTableName();
        $sql .= "(" . implode(", ",$this->_tableInstance->getOutputtedFieldsNames()) . ") \n VALUES \n";
        return $sql;
    }

    private function getTableName() {
        $tableName = $this->_tableInstance->getTableName();
        $tableName = str_replace(";","",$tableName);
        return $tableName;
    }

    private function getQueryPlaceholderString($numberOfRecords){
        $recordPlaceholders = substr(str_repeat('?,', count($this->_tableInstance->getOutputtedFields())), 0, -1);
        return substr(str_repeat("(". $recordPlaceholders ."),\n" , $numberOfRecords), 0,-2);
    }

    public function run(){
        $sqlString = $this->getSqlInsertString();
        $sqlString .= $this->getQueryPlaceholderString($this->_batchSize);

        $pdoConnection = $this->getDbConnection();
        $pdoStatement = $pdoConnection->prepare($sqlString);

        $iteration = 1;
        $done      = false;
        $records   = [];

        While (!$done) {
            $this->_tableInstance->setIteration($iteration);

            //Get and format the values of the current record
            $record = $this->getValuesFromGenerators($iteration);
            $record = array_map([$this, 'formatValue'], $record);
            //store the records in an array
            array_push($records, $record);

            $iteration++;
            $this->_tableInstance->setIteration($iteration);

            //Querying the table instance for the stopping message
            $done = $this->_tableInstance->mustStopProcess();

            //for large number of records, multiple files are generated
            if ($iteration % $this->_batchSize == 0) {
                $pdoStatement->execute($records);
                $records = [];
            }

        }
        //write the remaining records
        if (count($records) > 0) {
            $sqlString = $this->getSqlInsertString();
            $sqlString .= $this->getQueryPlaceholderString(count($records));

            $pdoConnection = $this->getDbConnection();
            $pdoStatement = $pdoConnection->prepare($sqlString);
            $pdoStatement->execute($records);
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
        if ($value == 'null') {
            return null;
        } else {
            return $value;
        }
    }


} 