<?php

namespace Model;

use SystemDate as SystemDate;

/**
 * CalendarFilter - Basic parameters to filter calendar data
 */
class CalendarFilter {
    public $events  = [];    
    /**
     * arms
     *
     * @var string[]
     */
    public $arms    = [];        
    /**
     * records
     *
     * @var string[]
     */
    public $records = [];        
    /**
     * status
     *
     * @var int
     */
    public $status  = -1;    
    /**
     * month
     *
     * @var int
     */
    public $month   = null;    
    /**
     * year
     *
     * @var int
     */
    public $year    = null;

    // public $fields = array();
    // public function getCustomFields(){
    //     return $this->fields;
    // }
    
    // public function addCustomField($name, $value){
    //     return $this->fields[$name] = $value;
    // }
    
    // public function getCustomFieldNames(){
    //     return array_keys($this->fields);
    // }
    
    
    public function __construct(array $records, array $arms, array $events, ? int $status = -1, int $year = -1, int $month = -1){
        if (isset($records) && count($records) > 0){
            $this->records = $records;
        }

        if (isset($arms) && count($arms) > 0){
            $this->arms = $arms;
        }

        if (isset($events) && count($events) > 0){
            $this->events = $events;
        }

        if ($status == null || ($status >= -1 && $status <= 4)){
            $this->status = $status;
        }
    
        if ($month >= 1 && $month <= 12){
            $this->month  = $month;
        }
    
        $yearRange = SystemDate::getYearRange();
        if (in_array($year, $yearRange)){
            $this->year = $year;
        }
    }
}