<?php

use Model\Calendar as Calendar;
use Model\CalendarFeed as CalendarFeed;
use Model\Project as Project;
use Eluceo\iCal\Domain\Entity\Calendar as DomainCalendar;
use Eluceo\iCal\Domain\Entity\Event as DomainEvent;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

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
                        
            $forms = [];
            if (empty($item->forms)) {
                $forms = [];
            } elseif (is_string($item->forms)) {
                $forms = explode(',', $item->forms);
            } elseif (is_array($item->forms)) {
                $forms = $item->forms;
            }

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
                implode(", ", $forms),
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
        $events = [];
        foreach ($calendar->items as $item) {
            $event = new DomainEvent();
            $event = $event
                ->setSummary($item->title)
                ->setDescription($item->description)
                ->setLocation(new Location($item->location));

            // Handle all-day vs timed events
            if (empty($item->event_time)) {
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', $item->event_date);
                $occurrence = new SingleDay(new Date($date));
            } else {
                $interval = new DateInterval('PT2H'); // This should be configurable
                $startDateTime  = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $item->event_date . ' ' . $item->event_time);
                $endDateTime    = $startDateTime->add($interval);
                $occurrence = new TimeSpan(new DateTime($startDateTime,false),  new DateTime($endDateTime, false));
            }
            $event = $event->setOccurrence($occurrence);
            $events[] = $event;
        }
        $domainCalendar = new DomainCalendar($events);
        $calendarComponent = (new CalendarFactory())->createCalendar($domainCalendar);
        return (string) $calendarComponent;
    }
}