<?php

namespace Model;

use Model\CalendarFeed as CalendarFeed;
use Model\CalendarLink as CalendarLink;

class Project {
    public $project_id;
    public $title;
    public $events = [];
    public $arms = [];
    public $statuses = [];
    public $feeds = [];
    public $links = [];
    
    public function __construct(int $project_id, string $title)
    {
        $this->project_id = $project_id;
        $this->title = $title;
    }

    public function getFeed(string $key) : ? CalendarFeed {
        foreach($this->feeds as $feed) {
            if ($feed->key == $key){
                return $feed;
            }
        }
        return null;
    }

    public function getLink(string $key) : ? CalendarLink {
        foreach($this->links as $link) {
            if ($link->key == $key){
                return $link;
            }
        }
        return null;
    }
}