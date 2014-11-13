<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SEB
 * Date: 13/09/13
 * Time: 14:52
 * To change this template use File | Settings | File Templates.
 */

namespace Simulacre\Generators;


class IntegerGenerator extends \Simulacre\Generators\BaseGenerator{

    protected $_minValue = 1;
    protected $_maxValue = 100;
    protected $_targetMean = 0;
    protected $_numBerOfRolls = 1;

    private $_total = 0;
    private $_mean = 0;
    private $_value;
    private $_delta;

    protected function getRandomNumber(){
        $totalRoll = 0;
        for($i=1;$i<=$this->_numBerOfRolls;$i++){
            $totalRoll += mt_rand($this->_minValue,$this->_maxValue);
        }

        return intval($totalRoll/$this->_numBerOfRolls);
    }

    protected function calculateValue(){

        $value = $this->getRandomNumber();

        if($this->_iteration>1 && $this->_targetMean >0){
            $delta = $this->_targetMean-($this->_total / $this->_iteration);
            $value += intval($delta * $this->_iteration);
            $value = max($value,$this->_minValue);

            $this->_delta = $delta;
        }

        $this->_value = $value;
        $this->_total += $value;
        $this->_mean = $this->_total/$this->_iteration;


        return $value;
    }

    public function getMean(){
        return $this->_mean;
    }

    public function getTotal(){
        return $this->_total;
    }

    public function toArray(){
        return array(
            'iteration' => $this->_iteration,
            'value'     => $this->_value,
            'total'     => $this->_total,
            'Mean'      => $this->_mean,
            'delta'     => $this->_delta,
        );
    }


}