<?php 

use REDCap as REDCap;
use Model\Project as Project;
use Model\CalendarFeed as CalendarFeed;
use Model\CalendarLink as CalendarLink;

class ProjectService {
    protected $module;
    protected $odm;

    function __construct(object $module)
    {
        $this->module = $module;

        $xml        = REDCap::getProjectXML($this->module->getProjectId(), true);
        $this->odm  = new \SimpleXMLElement($xml);
    }

    function getProject(int $pid = -1, bool $includeChildren = true) : Project {
        $pid   = $this->module->getProjectId();
        $title = REDCap::getProjectTitle();

        $project = new Project($pid, $title);

        if ($includeChildren){
            $project->events    = $this->getStudyEventDefs();
            $project->arms      = $this->getProtocolArms();
            $project->statuses  = $this->getEventStatuses();
            $project->feeds     = $this->getFeeds();
            $project->links     = $this->getLinks();
        }

        return $project;
    }

    function getMetaDataVersion(){
        return $this->odm->Study->MetaDataVersion;
    }

    function getProtocolArms(bool $includeChildren = true) : array {
        $arms = [];

        $metaData = $this->getMetaDataVersion();        
        if ($metaData) {
            foreach($metaData->StudyEventDef as $index => $studyEventDef){
                $armNum  = (integer) $studyEventDef->attributes('redcap', true)->{'ArmNum'};
                $armName = (string) $studyEventDef->attributes('redcap', true)->{'ArmName'};

                if (is_numeric($armNum)){
                    $arms[$armNum] = $armName;
                }
            }            
        }

        return $arms;
    }

    function getStudyEventDefs(bool $includeChildren = true) : array {
        $events = [];
        
        $metaData = $this->getMetaDataVersion();        
        if ($metaData){
            foreach($metaData->StudyEventDef as $index => $studyEventDef){
                $uniqueName = (string) $studyEventDef->attributes('redcap', true)->{'UniqueEventName'};

                $events [] = [
                    'name' => (string) $studyEventDef->attributes()->{'Name'},
                    'unique_name' => $uniqueName,
                    'event_id' => REDCap::getEventIdFromUniqueEvent($uniqueName)
                ];
            }
        }
        return $events;
    }

    function getEventStatuses() {
        return array(
            null => "Not Specified"
            , "0" => "Due Date"
            , "1" => "Scheduled"
            , "2" => "Confirmed"
            , "3" => "Cancelled"
            , "4" => "No Show"                
        );
    }

    function getFeeds() : array {
        $settings = $this->module->getSubSettings("calendar-feeds");
        $feeds    = [];

        $feeds[] = new CalendarFeed("default", "Default");
        
        foreach($settings as $setting){
            $feed = new CalendarFeed($setting['feed-key'], $setting['feed-name']);   
            $feed->setTemplates($setting['feed-title-template'], $setting['feed-description-template'], $setting['feed-location-template']);

            $feeds[] = $feed;
        }
        return $feeds;
    }

    function getLinks() : array {
        $settings = $this->module->getSubSettings("calendar-links");
        $links = [];
        foreach($settings as $setting){
            $link = new CalendarLink($setting['link-name'], $setting['link-params'], $setting['link-key']);
            $link->access_level = $setting['link-access-level'];
            $link->enabled      = ($setting['link-enabled'] == "true");

            $links[] = $link;
        }
        return $links;
    }
}