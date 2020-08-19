<?php 

use Model\CalendarRequest as CalendarRequest;
use Model\Calendar as Calendar;
use TemplateEngine as TemplateEngine;
use REDCap as REDCap;
use ClanCats\Hydrahon\Builder as Builder;
use ClanCats\Hydrahon\Query\Sql\Func as Func;



class CalendarService {
    protected $module;

    function __construct(object $module)
    {
        $this->module = $module;
    }

    function getCalendar(CalendarRequest $request) : Calendar{
        $sql = new Builder('mysql', [$this, 'getCalendarData']);

        $query = $sql->table('redcap_events_calendar', 'c')->select([
            'c.cal_id', 
            'c.project_id', 
            'c.event_id', 
            'c.record', 
            'c.event_date', 
            'c.event_time', 
            'c.event_status', 
            'c.notes'
        ]);

        $query->where('c.project_id', $request->project->project_id);
    
        $query->addField('descrip', 'event_descrip');
        $query->join('redcap_events_metadata as e', 'e.event_id', '=', 'c.event_id');
		
		$query->addField("arm_name");
		$query->addField("arm_num");
		$query->join('redcap_events_arms as a', 'e.arm_id', '=', 'a.arm_id');
        
        $filter = $request->filter;
        if ($filter != null){
    		if (count($filter->records) > 0){
                $query->where('c.record', 'in', $filter->records);
            }  
                      
            if (count($filter->arms) > 0){
                $query->where('a.arm_num', $filter->arms[0]);
            }
            
            if (count($filter->events) > 0){
                $query->where('c.event_id', $filter->events[0]);
    		}
    
            if ($filter->status === null){
                $query->whereNull('c.event_status');
            }
    		elseif ($filter->status >= 0){
                $query->where('c.event_status', $filter->status);
    		}
            
    		if ($filter->year != null){
                $query->where(new Func('year', 'c.event_date'), $filter->year);
    		}
    
    		if ($filter->month != null){
                $query->where(new Func('month', 'c.event_date'), $filter->month);
    		}
    			
    		// if (count($filter->getCustomFields()) > 0){
    		// 	foreach($filter->getCustomFields() as $name => $value){
    		// 		$query->addSelect("$name.value as $name");
    		// 		$query->innerJoin('c', 'redcap_data', $name, "c.project_id = $name.project_id and c.record = $name.record and $name.field_name = '$name'");
    
    		// 		if (strlen($value) > 0){
    		// 			$query->andWhere("$name.value = :$name");
    		// 			$query->setParameter($name, $value);
    		// 		}
    		// 	}
    		// }
        }
        
        $calendar = new Calendar($filter);
        $calendar->setItems($query->execute());

        return $calendar;
    }

    function getCalendarData($query, $queryString, $queryParameters) : array      
    {
        $data = [];
        
        $results = $this->module->query($queryString, $queryParameters);
        if ($results->num_rows > 0){
            while($item = $results->fetch_object('Model\\CalendarItem')){
                $data[] = $item;
            }
        }
        return $data;
    }

    public function getFormattedCalendar(CalendarRequest $request){
        // Get the basic calendar data from REDCap....
        $calendar = $this->getCalendar($request);
        
        // If the calendar has data then
        if (count($calendar->items) > 0){
            // Get the the various calendar statuses and the correspoinding feed (default)
            $statuses = $request->project->statuses;
            $feed     = $request->project->getFeed($request->feed);  

            $data = $this->getContextData($calendar->items, $feed->data_fields);
            // For each of the calendar items
            foreach($calendar->getItems() as $item){
                // Assign the status name (Due Date, Completed, etc.)
                $item->event_status_name = $statuses[$item->event_status];

                // If the event ID is specified (not an ad-hoc calendar item)
                if ($item->event_id > 0){
                    // Get the form names associated with the event.
                    $forms = $this->getEventForms($item->event_id);
                    $item->forms = array_column($forms, 'form_name');
                }

                $context = array_merge((array) $item, [ 'data' => $data[$item->record] ]);

                // Format the title, description, etc.
                $item->title        = TemplateEngine::renderTemplate($feed->title_template, $context);
                $item->description  = TemplateEngine::renderTemplate($feed->description_template, $context);
                $item->location     = TemplateEngine::renderTemplate($feed->location_template, $context);
            }
        }
        
        return $calendar;
    }

    public function getContextData(array $calendarItems, array $fields) : array{
        // If no items or extra fields are defined then don't call the REDCAP API
        if (count($calendarItems) == 0 || count($fields) == 0){
            return [];
        }

        // Get the uniques records in the calendar data set...
        $records = array_values(array_unique(array_column($calendarItems, "record")));

        // Using REDCap API, get the data corresponding to the fields of interest (PID assumes current project)
        $data = REDCap::getData([
                'records'  => $records,
                'fields'   => $fields
            ]);

        // Flatten the data, using the last-known value as the default
        foreach($data as $record_id => $record){
            foreach($record as $event_id => $event){
                // Traverse only the event_id indexed fields
                if (is_numeric($event_id) > 0){                   
                    // For each unique field defined in the event
                    foreach($event as $field => $value){
                        // If the field has a value (or no value exists) then promote it to the root of the record
                        if ($data[$record_id][$field] == null || strlen($value) > 0){
                            $data[$record_id][$field] = $value;
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function getEventForms(int $event_id) : array {
        $sql   = new Builder('mysql', [$this, 'fetchAll']);
        $query = $sql->table('redcap_events_forms', 'ef')->select([
            'ef.event_id', 
            'ef.form_name'
        ]);
        $query->where('ef.event_id', $event_id);

        return $query->execute();
    }

    public function fetchAll($query, $queryString, $queryParameters) {
        $data = [];
        $results = $this->module->query($queryString, $queryParameters);
        if ($results->num_rows > 0){
            while($item = $results->fetch_assoc()){
                $data[] = $item;
            }
        }
        return $data;
    }
}