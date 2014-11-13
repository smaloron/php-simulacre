<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SEB
 * Date: 13/09/13
 * Time: 15:56
 * To change this template use File | Settings | File Templates.
 */

namespace Simulacre\Generators;


class RealGenerator extends \Simulacre\Generators\IntegerGenerator{

    protected $_numberOfDecimals = 2;

    protected function getRandomNumber(){
        $totalRoll = 0;
        $minValue = $this->_minValue*pow(10,$this->_numberOfDecimals);
        $maxValue = $this->_maxValue*pow(10,$this->_numberOfDecimals);

        for($i=1;$i<=$this->_numBerOfRolls;$i++){
            $totalRoll += mt_rand($minValue,$maxValue) / pow(10,$this->_numberOfDecimals);
        }

        return round($totalRoll/$this->_numBerOfRolls, $this->_numberOfDecimals);
    }
}