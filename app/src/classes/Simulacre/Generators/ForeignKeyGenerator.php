<?php
/**
 * Created by PhpStorm.
 * User: seb
 * Date: 27/02/2014
 * Time: 08:08
 */

namespace Simulacre\Generators;


class ForeignKeyGenerator extends \Simulacre\Generators\FromDataBaseGenerator
{

    //protected $fkFieldName;
    protected $numberOfValuesByRecords = null;

    private $minNumberOfValues = null;
    private $maxNumberOfValues = null;
    protected $dataSet;

    private $numberOfRecordsLeft;


    public function __construct($params)
    {
        $this->hydrate($this, $params);
        $this->setNumberOfRecords();

    }

    private function setNumberOfRecords()
    {
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
                $numLeft = $numLeft==null?1:$numLeft;
            }
        }
        $this->numberOfRecordsLeft = $numLeft;
    }

    public function calculateValue()
    {
        if (!isset($this->dataSet)) {
            $this->dataSet = $this->getDataset();
        }

        $this->currentRecord = $this->dataSet[0];
        $currentValue = $this->currentRecord[$this->fieldName];


        if ($this->numberOfRecordsLeft == 0) {
            array_shift($this->dataSet);
            //Reset the number of records left
            $this->setNumberOfRecords();
        } else {
            $this->numberOfRecordsLeft--;
        }

        if (count($this->dataSet) == 0) {
            $this->_tableInstance->stopProcess();
        }

        return $currentValue;

    }

    private function getNumberOfRecords()
    {
        if (is_integer($this->numberOfRecordsPerFk)) {
            return $this->numberOfRecordsPerFk;
        }
    }

    public function getNumberOfRecordsLeft(){
        return $this->numberOfRecordsLeft;
    }
} 