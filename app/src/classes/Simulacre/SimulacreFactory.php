<?php
/**
 * Created by PhpStorm.
 * User: seb
 * Date: 13/10/2014
 * Time: 13:48
 */

namespace Simulacre;


class SimulacreFactory {

    /**
     * @var array
     */
    private $_generatorClassMap;
    private $_outputClassMap;
    private $_genericClassMap;

    public function __construct(array $classMap){
        $this->_generatorClassMap = $classMap['generators'];
        $this->_outputClassMap = $classMap['outputs'];
        $this->_genericClassMap = $classMap['generics'];
    }

    private function createInstance($params, $classKey, $classMap){
        $instanceKey = $params[$classKey];
        if (!array_key_exists($instanceKey,$classMap)){
            throw new \ErrorException("The key ". $instanceKey. " does not exist in the classmap");
        }
        $classDefinition = $classMap[$instanceKey];
        if(array_key_exists('params',$classDefinition)){
            $params = array_merge($classDefinition['params'],$params);
        }

        $class = $classDefinition['className'];
        $instance =  new $class ($params);

        return $instance;
    }

    public function createGenerator($params, SimulacreTable $tableInstance) {
        $instance = $this->createInstance($params,'generator', $this->_generatorClassMap);
        $instance->setTableInstance($tableInstance);
        /*
        $validatorInstance = Core::createInstance('validator');
        $instance->setValidator($validatorInstance);
        */
        return $instance;
    }

    public function createOutput(SimulacreTable $tableInstance ){
        $params = $tableInstance->getOutputParams();
        //$outputMethod = $params['outputMethod'];
        $instance = $this->createInstance($params,'outputMethod', $this->_outputClassMap);
        $instance->setTableInstance($tableInstance);
        return $instance;
    }

    public function create($key, $params = []){
        $instance = $this->createInstance($params,$key, $this->_genericClassMap);
        return $instance;
    }


} 