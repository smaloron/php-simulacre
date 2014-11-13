<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SEB
 * Date: 13/09/13
 * Time: 16:46
 * To change this template use File | Settings | File Templates.
 */

namespace Simulacre\Generators;


abstract class  BaseGenerator extends \Simulacre\SimulacreBaseClass{

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
     * @var bool specifies wether the generator value should be included in the output
     *           Some generator value can be used by other generators and should not map to a database field
     */
    protected $_includedInOutput = true;

    protected $_validator;
    /**
     * @var \Simulacre\SimulacreTable
     */
    protected $_tableInstance;

    public function setValidator($validatorInstance){
        $this->_validator = $validatorInstance;
    }

    public function setTableInstance(\Simulacre\SimulacreTable $tableInstance){
        $this->_tableInstance = $tableInstance;
    }

    /**
     * This method provides the actual code which generate the value
     * It must be overridden by the children class extending BaseGenerator
     * @return mixed
     */
    protected abstract function calculateValue();

    protected  function generateValue(){
        $this->_iteration ++;
        if($this->_percentOfNull >0 && mt_rand(1,100)<$this->_percentOfNull){
            $value = null;
        } else {
            $value = $this->calculateValue();
        }
        $this->_generatedValue = $value;
    }

    /**
     * Generate a new value or returns the previously generated value for this iteration
     * @param int $iteration
     * @return mixed
     */
    public function getValue($iteration = 0){
        if($this->_generatedValue == null || $iteration=0 || $iteration != $this->_iteration){
            $this->generateValue();
        }
        return $this->_generatedValue;
    }

    /**
     * Returns the generated value formatted by the closures defined in the formatters array
     * @param int $iteration
     * @return mixed
     */
    public function getFormattedValue ($iteration = 0){
        $value = $this->getValue($iteration);
        $formatedValue = $this->applyFormaters($value);
        return $formatedValue;
    }

    /**
     * Apply all the closures in the formatters array to the generated value
     * @return mixed
     */
    private function applyFormaters (){
        $value = $this->_generatedValue;
        for($i=0; $i<count($this->_formatters); $i++){
            if(is_callable($this->_formatters[$i])){
                $value = $this->_formatters[$i]($value, $this);
            }
        }
        return $value;
    }

    public function getIteration(){
        return $this->_iteration;
    }

    /**
     * Lookup a value in a previously defined generator
     *
*@param $generatorName String
     * @param $key String
     *
     * @return mixed
     */
    public function getLookUpValue($generatorName, $key = null){
        $generator = $this->getForeignGenerator($generatorName);
        $value = $generator->getValue($this->_iteration);
        if(isset($key) && method_exists($generator,'lookUpFromArray')){
            $value = $generator->lookUpFromArray($key, $this->_iteration);
        }
        return $value;
    }

    /**
     * @param $generatorFieldName String
     * @return \Simulacre\Generators\BaseGenerator
     */
    public function getForeignGenerator($generatorFieldName){
        return $this->_tableInstance->getFieldByName($generatorFieldName);
    }

    public function isToBeOutputted(){
        return $this->_includedInOutput;
    }


}