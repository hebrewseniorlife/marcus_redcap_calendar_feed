<?php

namespace Model;

use Model\CalendarFeed as CalendarFeed;
use Model\CalendarLink as CalendarLink;
use Model\StudyEvent as StudyEvent;

/**
 * Project - Project context required to create a calendar feed
 */
class Project {    
    /**
     * project_id
     *
     * @var int
     */
    public $project_id;    
    /**
     * title
     *
     * @var string
     */
    public $title;    
    /**
     * events
     *
     * @var StudyEvent[]
     */
    public $events = [];    
    /**
     * arms
     *
     * @var string[]
     */
    public $arms = [];    
    /**
     * statuses
     *
     * @var string[]
     */
    public $statuses = [];    
    /**
     * feeds
     *
     * @var CalendarFeed[]
     */
    public $feeds = [];    
    /**
     * links
     *
     * @var CalendarLink[]
     */
    public $links = [];
    
    public function __construct(int $project_id, string $title)
    {
        $this->project_id = $project_id;
        $this->title = $title;
    }
    
    /**
     * getFeed - Returns CalendarFeed by key
     *
     * @param  mixed $key
     * @return CalendarFeed
     */
    public function getFeed(string $key) : ? CalendarFeed {
        foreach($this->feeds as $feed) {
            if ($feed->key == $key){
                return $feed;
            }
        }
        return null;
    }
    
    /**
     * getLink - Returns CalendarLink by key
     *
     * @param  mixed $key
     * @return CalendarLink
     */
    public function getLink(string $key) : ? CalendarLink {
        foreach($this->links as $link) {
            if ($link->key == $key){
                return $link;
            }
        }
        return null;
    }
    
    /**
     * getEvent
     *
     * @param  mixed $id
     * @return array
     */
    public function getEvent(int $id) : ? array {
        foreach($this->events as $event){
            if ($event->event_id == $id){
                return $event;
            }
        }
        
        return null;
    }

    
    /**
     * getEvents
     *
     * @param  array $names
     * @return StudyEvent[]
     */
    public function getEvents(array $uniqueNames) : ? array {
        $results = [];
        foreach($uniqueNames as $name){
            foreach($this->events as $event){
                if ($event->unique_name == $name){
                   array_push($results, $event);
                }
            }
        }
        return $results;
    }
}