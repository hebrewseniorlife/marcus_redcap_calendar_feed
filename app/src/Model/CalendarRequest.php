<?php 

namespace Model;

use Model\CalendarFilter as CalendarFilter;
use Model\Project as Project;

/**
 * CalendarRequest  - Represents the context of a request for a calendar (project, filter params, format, etc.)
 */
class CalendarRequest {    
    /**
     * project
     *
     * @var Project
     */
    public $project;    
    /**
     * filter
     *
     * @var CalendarFilter
     */
    public $filter;    
    /**
     * format
     *
     * @var string
     */
    public $format;    
    /**
     * feed
     *
     * @var string
     */
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