<?php
/**
 * Created by PhpStorm.
 * User: seb
 * Date: 27/02/2014
 * Time: 08:08
 */

namespace Simulacre\Generators;


use Simulacre\Simulacre;

class FromDataBaseGenerator extends \Simulacre\Generators\BaseGenerator
{

    /**
     * @var String
     */
    protected $sqlString;
    /**
     * @var String
     */
    protected $dataSource;
    /**
     * @var String
     */
    protected $fieldName;
    /**
     * @var array
     */
    protected $queryParameters;
    /**
     * @var array
     */
    protected $dataSet;
    /**
     * @var \PDO
     */
    private $pdoConnection;
    /**
     * @var int
     */
    private $numberOfRecords;
    /**
     * @var int
     */
    protected $currentRecord;

    protected $newQueryForEachIteration = false;

    /**
     * @return \PDO
     * get a PDO connection from an associative array referencing available connections
     */
    protected function getPdoConnection()
    {
        if (!isset($this->pdoConnection)) {
            $this->pdoConnection = Simulacre::getDataSources($this->dataSource);
        }
        return $this->pdoConnection;
    }

    public function calculateValue()
    {
        $dataSet = $this->getDataset();
        $this->currentRecord = $dataSet[mt_rand(0, $this->numberOfRecords - 1)];
        if (isset($this->fieldName)) {
            return $this->currentRecord[$this->fieldName];
        } else {
            return $this->currentRecord[0];
        }
    }

    protected function getDataset()
    {
        if ($this->newQueryForEachIteration || !isset($this->dataSet)) {
            $this->dataSet = $this->populateDataSet();
        }
        return $this->dataSet;
    }


    /**
     * @return array
     * get the query parameters as an associative array
     * those parameters are set from the variable queryParameters which is an array referencing constant values
     * or links to other generators
     *
     * example
     *
     * array(
     *      'queryParameters' => array(
     *          'age'       => 30,
     *          'lookupSex' => array('generatorName' => 'sex'),
     *       )
     * );
     */
    private function getQueryParameters()
    {
        $dbParameters = array();
        if (count($this->queryParameters) > 0) {
            foreach ($this->queryParameters as $key => $value) {
                if (is_array($value)) {
                    $lookUpValue = $this->_tableInstance->getLookUpValue($value['generatorName'], $this->_iteration);
                    $bdParameters[$key] = $lookUpValue;
                    //If the parameters depends on generator values, the query must be executed for each iteration
                    $this->newQueryForEachIteration = true;
                } else {
                    $bdParameters[$key] = $value;
                }
            }
        }

        return $dbParameters;
    }

    /**
     * @return array
     * Execute the query and returns an array representing the dataset
     */
    private function populateDataSet()
    {
        $queryParameters = $this->getQueryParameters();
        $pdoConnection = $this->getPdoConnection();
        $pdoStatement = $pdoConnection->prepare($this->sqlString);
        $pdoStatement->execute($queryParameters);
        $dataSet = $pdoStatement->fetchAll($pdoConnection::FETCH_BOTH);
        $this->numberOfRecords = count($dataSet);
        return $dataSet;
    }

    /**
     * @param $key String or integer
     * @return mixed
     * Look up a value from an associative array dataset
     * the param $fieldName represents the key
     */
    public function lookUpFromArray($key)
    {
        return $this->currentRecord[$key];
    }

} 