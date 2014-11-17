<?php

    namespace Simulacre\Generators;
    /**
     * Generate a random date Time from startDate to EndDate
     *
     * Class DateGenerator
     * @package Simulacre\Generators
     */

    class DateGenerator extends BaseGenerator
    {

        protected $_startDate;
        protected $_endDate;
        protected $_dateFormat = 'Y-m-d';
        protected $_timeZone = 'Europe/Paris';
        protected $_outputFormat = 'Y-m-d H:i:s';


        public function __construct($params) {
            $this->hydrate($this, $params);
            date_default_timezone_set($this->_timeZone);
        }

        public function calculateValue() {
            $startTimeStamp  = date_timestamp_get(date_create_from_format($this->_dateFormat, $this->_startDate));
            $endTimeStamp    = date_timestamp_get(date_create_from_format($this->_dateFormat, $this->_endDate));
            $randomTimeStamp = mt_rand($startTimeStamp, $endTimeStamp);
            $dateValue       = new \DateTime('@' . $randomTimeStamp);

            return $dateValue->format($this->_outputFormat);
        }

    }