<?php 

namespace Model;

use Model\CalendarFilter as CalendarFilter;
use Model\Project as Project;

class CalendarRequest {
    public $project;
    public $filter;
    public $format;
    public $feed;

    public const DEFAULT_FEED   = "default";
    public const DEFAULT_FORMAT = "html";

    function __construct(Project $project = null)
    {  
       $this->project = $project;
       $this->filter  = null;
       
       $this->feed      = CalendarRequest::DEFAULT_FEED;
       $this->format    = CalendarRequest::DEFAULT_FORMAT;
    }
}