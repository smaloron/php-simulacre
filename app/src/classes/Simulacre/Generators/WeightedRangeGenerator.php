<?php
/**
 * Created by PhpStorm.
 * User: seb
 * Date: 25/02/2014
 * Time: 08:31
 */

namespace Simulacre\Generators;


class WeightedRangeGenerator extends \Simulacre\Generators\BaseGenerator
{

    protected $_weightedRange = array();

    protected function calculateValue() {
        $rnd = mt_rand(1, (int)array_sum($this->_weightedRange));
        foreach ($this->_weightedRange as $key => $value) {
            $rnd -= $value;
            if ($rnd <= 0) {
                return $key;
            }
        }
    }
}