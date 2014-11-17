<?php
    namespace Simulacre;


    abstract class SimulacreBaseClass
    {

        public function __construct($params) {
            $this->hydrate($this, $params);
        }

        /**
         * this function sets the public or protected members of a class $target
         * to the values contained in an associative array $param
         *
         * @param $target object
         * @param $params array
         */
        protected function hydrate($target, $params) {
            foreach ($params as $key => $val) {
                if (property_exists($this, $key)) {
                    $propertyName          = $key;
                    $target->$propertyName = $val;
                } else if (property_exists($this, '_' . $key)) {
                    $propertyName          = '_' . $key;
                    $target->$propertyName = $val;
                }
            }
        }
    }