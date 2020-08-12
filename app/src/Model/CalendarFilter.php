<?php
/**
 * CalendarFilter.php
 *
 */

namespace Model;

use SystemDate as SystemDate;

class CalendarFilter {
    public $events  = [];
    public $arms    = [];    
    public $records = [];    
    public $status  = -1;
    public $month   = null;
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
    
    
    public function __construct(array $records, array $arms, array $events, int $status = -1, int $year = -1, int $month = -1){
        if (isset($records) && count($records) > 0){
            $this->records = $records;
        }

        if (isset($arms) && count($arms) > 0){
            $this->arms = $arms;
        }

        if (isset($events) && count($events) > 0){
            $this->events = $events;
        }

        if ($status >= 0 && $status <= 4){
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