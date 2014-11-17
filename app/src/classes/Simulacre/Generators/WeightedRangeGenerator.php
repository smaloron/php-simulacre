<?php

    namespace Simulacre\Generators;

    /**
     * Generate a random value from an associative array of weights and accepted values.
     * The keys of the array represents the accepted values and the values of the array represents the numerical weight.
     *
     * Class WeightedRangeGenerator
     * @package Simulacre\Generators
     */

    class WeightedRangeGenerator extends \Simulacre\Generators\BaseGenerator
    {

        protected $_weightedRange = array();

        protected function calculateValue() {
            $value = null;
            $rnd = mt_rand(1, (int)array_sum($this->_weightedRange));
            foreach ($this->_weightedRange as $key => $value) {
                $rnd -= $value;
                if ($rnd <= 0) {
                    $value = $key;
                    break;
                }
            }

            return $value;
        }
    }