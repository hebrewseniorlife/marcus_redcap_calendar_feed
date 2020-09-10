<?php

use Model\Calendar as Calendar;
use Model\CalendarFeed as CalendarFeed;
use Model\Project as Project;
use Eluceo\iCal\Component\Calendar as vCalendar;
use Eluceo\iCal\Component\Event as vEvent;
use Eluceo\iCal\Component\Timezone as vTimezone;
use DateTime;

/**
 * CalendarFeedWriter - Class provides functions to trasform Calendars into various formats
 */
class CalendarFeedWriter {    
    /**
     * createCsv - Creates a CSV-formatted string based on the Calendar provided
     *
     * @param  mixed $calendar
     * @return string
     */
    public function createCsv(Calendar $calendar) : string {
        ob_start();
        
        $file = fopen("php://output", "w");
        
        $columns = [
            "record", 
            "event_id", 
            "event_descrip", 
            "event_status", 
            "event_status_name", 
            "event_date", 
            "event_time", 
            "event_datetime", 
            "notes", 
            "forms",
            "title",
            "desription",
            "location"
        ];
       
        
        // $customFields = $calendar->getFilter()->getCustomFields();

        // if (count($customFields) > 0){
        //     foreach($customFields as $name => $value) {
        //         array_push($columns, $name);
        //     }
        // }
        
        fputcsv($file, $columns);
        
        foreach($calendar->items as $item){
            $eventDateTime = new \DateTime("$this->event_date $this->event_time");

            $row = [
                $item->record,
                $item->event_id,
                $item->event_descrip,
                $item->event_status,
                $item->event_status_name,
                $item->event_date,
                $item->event_time,
                $eventDateTime->format("c"),
                str_replace("\n", " ", $item->notes),
                implode(", ", $item->forms),
                $item->title,
                $item->description,
                $item->location
            ];
        
            // if (count($customFields) > 0){
            //     foreach($customFields as $name => $value) {
            //         array_push($row, $item->$name);
            //     }
            // }
            
            fputcsv($file, $row);
        }
        
        fclose($file);
        
        return ob_get_clean();
    }
    
    /**
     * createICal  -  Creates a iCAL-formatted string based on the Calendar provided
     *
     * @param  mixed $calendar
     * @param  mixed $project
     * @return string
     */
    public function createICal(Calendar $calendar, Project $project) : string {
        $vCalendar = new vCalendar($project->title);
        $vCalendar->setName($project->title);

        foreach($calendar->items as $item){
            $uniqueId = $item->cal_id.'_'.$item->record.'_'.$item->event_id;

            $vEvent = new vEvent($uniqueId);
            $vEvent
                ->setSummary($item->title)
                ->setDescription($item->description)
                ->setLocation($item->location);
            
            //$vEvent->setUseTimezone(true);

            if (empty($item->event_time)){
                $vEvent->setNoTime(true);
                $startDateTime  = DateTime::createFromFormat('Y-m-d', $item->event_date);
                $endDateTime    = DateTime::createFromFormat('Y-m-d', $item->event_date);
            }
            else{
                $offset         = new DateInterval('PT1H');
                $startDateTime  = DateTime::createFromFormat('Y-m-d H:i', $item->event_date.' '.$item->event_time);
                $endDateTime    = DateTime::createFromFormat('Y-m-d', $item->event_date)->add($offset);
            }

            $vEvent->setDtStart($startDateTime);
            $vEvent->setDtEnd($endDateTime);

            $vCalendar->addComponent($vEvent);
        }

        return $vCalendar->render();
    }
}