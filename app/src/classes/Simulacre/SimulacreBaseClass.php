<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SEB
 * Date: 13/09/13
 * Time: 14:26
 * To change this template use File | Settings | File Templates.
 */

namespace Simulacre;


abstract class SimulacreBaseClass {

    public function __construct($params){
       $this->hydrate($this,$params);
    }

    /**
     * @param $target object
     * @param $params array
     *
     * this function sets the public or protected members of a class $target
     * to the values contained in an associative array $param
     */
    protected function hydrate ($target, $params){
        foreach ($params as $key=>$val){
            if(property_exists($this,$key)){
                $propertyName = $key;
                $target->$propertyName = $val;
            }else if (property_exists($this,'_'.$key)){
                $propertyName = '_'.$key;
                $target->$propertyName = $val;
            }
        }
    }
}