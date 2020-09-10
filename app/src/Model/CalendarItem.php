<?php

namespace Model;

/**
 * CalendarItem  - Represents a single scheduled calendar event in REDCap
 */
class CalendarItem{
    /*
        REDCap SQL Fields
    */
    public $cal_id;
    public $record;
    public $event_id;
    public $project_id;
    public $event_date;
    public $event_time;
    public $event_status;
    public $event_status_name;
    public $notes;
    public $event_descrip;
    public $arm_name;    
    public $forms;

    /*
        Calendar Fields
    */
    public $title;
    public $description;
    public $location;
}