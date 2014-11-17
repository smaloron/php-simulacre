<?php

    namespace Simulacre\Output;

    class  CsvFileOutput extends BaseFileOutput
    {

        protected function setContentHeader() {
            $fieldsNames = array_map([$this, 'formatValue'], $this->_tableInstance->getOutputtedFieldsNames());

            return implode($this->_fieldSeparator, $fieldsNames);
        }
    }