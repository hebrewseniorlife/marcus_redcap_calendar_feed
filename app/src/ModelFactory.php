<?php 

use Model\CalendarRequest as CalendarRequest;
use Model\CalendarFilter as CalendarFilter;
use Model\Project as Project;
use ProjectService as ProjectService;
use UrlBuilder as UrlBuilder;
use ValidationException as ValidationException;

use League\Uri\QueryString as QueryString;
use Symfony\Component\HttpFoundation\Request as Request;

/**
 * ModelFactory - Factory class for creating various module Model classes
 */
class ModelFactory {
    protected $module;
    protected $projectService;

    function __construct(object $module)
    {
        $this->module           = $module;
        $this->projectService   = new ProjectService($this->module);
    }

    function createCalendarRequestByQuery(Request $request) : CalendarRequest {
        $project = $this->createProject($request);
        return $this->createCalendarRequest($project, (array) $request->query->all());
    }

    function createCalendarRequestByLink(Request $request) : CalendarRequest{
        $project    = $this->createProject($request);
        $link       = $project->getLink($request->query->get("key"));

        if ($link == null){
            throw new ValidationException("Link key not found in project.");
        }

        if (!$link->enabled){
            throw new ValidationException("Link specified is not currently enabled.");
        }

        if ($link->access_level != "public"){
            throw new ValidationException("The link access rights are set to private.  Private links are not currently supported.");
        }
            
        $params = QueryString::extract($link->params);
        $feed   = $params["feed"];
        if (empty($feed)){
            throw new ValidationException("A named feed is required for API link requests.");
        }
        else{
            $feed = $project->getFeed($feed);
            if ($feed == null){
                throw new ValidationException("The named feed does not exist in this project.");
            }
            else{
                if ($feed == \Model\CalendarFeed::DEFAULT_KEY){
                    throw new ValidationException("Default feeds are not available for public link requests.");
                }
            }
        }
            
        return $this->createCalendarRequest($project, $params);
    }

    function createCalendarRequest(Project $project = null, array $params = []) : CalendarRequest {
        $calendarRequest = new CalendarRequest($project);

        $calendarRequest->filter = $this->createFilter($params);
        $calendarRequest->format = (!empty($params["format"])) ? $params["format"] : CalendarRequest::DEFAULT_FORMAT;
        $calendarRequest->feed   = (!empty($params["feed"])) ? $params["feed"] : CalendarRequest::DEFAULT_FEED;

        return $calendarRequest;
    }
    
    /**
     * createProject - Crreates Project object based on the HTTP Request proided
     *
     * @param  mixed $request
     * @return Project
     */
    function createProject(Request $request) : Project {
        $project = $this->projectService->getProject($request->query->get("pid", -1));

        if ($project != null){
            $urlBuilder = new UrlBuilder($this->module);
            // For each of the feeds defined, format a proper URL for reference later
            foreach ($project->feeds as $feed){
                $feed->url = $urlBuilder->createFeedUrl($feed);
            }
            // For each of the links defined, format a proper URL for reference later
            foreach ($project->links as $link){
                $link->url = $urlBuilder->createLinkUrl($link);
            }
        }

        return $project;
    }
    
    /**
     * createFilter  - Creates a CaldendarFilter object based on the HTTP params provided
     *
     * @param  mixed $params
     * @return CalendarFilter
     */
    function createFilter(array $params) : CalendarFilter {
        // Extract the participant records...
        $records = ($params['records'] != null) ? explode(',', $params['records']) : [];
        $records = array_filter($records);

        $arms = $params['arms'];
        if (!is_array($arms)) {
            $arms = (count(trim($arms)) > 0) ? explode(',', $arms) : [];
        }
        $arms = array_filter($arms);

        $events = $params['events'];
        if (!is_array($events)) {
            $events = (count(trim($events)) > 0) ? explode(',', $events) : [];
        }
        $events = array_filter($events);
        

        $status = -1;
        if (preg_match('/^\d+$/', $params["status"])){
            $status = intval($params["status"]);
        }
        elseif (isset($params["status"]) && $params["status"] == ""){
            $status = null;
        }

        $month      = (preg_match('/^\d+$/', $params["month"])) ? intval($params["month"]) : -1;
        $year       = (preg_match('/^\d+$/', $params["year"])) ? intval($params["year"]) : -1;

        return new CalendarFilter($records, $arms, $events, $status, $year, $month);
    }
}