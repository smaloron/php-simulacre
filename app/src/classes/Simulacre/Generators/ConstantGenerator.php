<?php
    namespace Simulacre\Generators;

    /**
     * Generate a constant value
     *
     * Class ConstantGenerator
     * @package Simulacre\Generators
     */
    class ConstantGenerator extends BaseGenerator
    {

        protected $_constantValue;

        public function calculateValue() {
            return $this->_constantValue;
        }
    }