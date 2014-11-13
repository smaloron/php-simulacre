<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SEB
 * Date: 13/09/13
 * Time: 15:56
 * To change this template use File | Settings | File Templates.
 */

namespace Simulacre\Generators;


class LookUpGenerator extends \Simulacre\Generators\BaseGenerator{
    protected $_lookUpFieldName;
    protected $_lookUpKey;

    public function calculateValue() {
        return $this->getLookUpValue($this->_lookUpFieldName, $this->_lookUpKey);
    }
}