<?php

namespace Simulacre\Output;

use Simulacre\Simulacre;
use Simulacre\SimulacreTable;

class  CsvFileOutput extends BaseFileOutput {

    protected function setContentHeader(){
        $fieldsNames = array_map([$this,'formatValue'],$this->_tableInstance->getOutputtedFieldsNames());
        return implode($this->_fieldSeparator,$fieldsNames);
    }
}