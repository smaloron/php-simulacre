<?php

    namespace Simulacre\Generators;

    /**
     * Looks up a value from another field's generator
     *
     * Class LookUpGenerator
     * @package Simulacre\Generators
     */

    class LookUpGenerator extends \Simulacre\Generators\BaseGenerator
    {
        protected $_lookUpFieldName;
        protected $_lookUpKey;

        public function calculateValue() {
            return $this->getLookUpValue($this->_lookUpFieldName, $this->_lookUpKey);
        }
    }