<?php

use Model\CalendarRequest as CalendarRequest;
use Model\CalendarFeed as CalendarFeed;
use Model\CalendarLink as CalendarLink;
use League\Uri\QueryString as QueryString;
use League\Uri\UriModifier as UriModifier;
use League\Uri\Uri as Uri;

class UrlBuilder {
    protected $module;

    public const DEFAULT_ACTION     = "view"; 
    public const DEFAULT_FEED       = "default";
    public const DEFAULT_PAGE       = "index.php";
    public const DEFAULT_BASE_URI   = APP_PATH_WEBROOT.'ExternalModules/';

    function __construct($module)
    {
        $this->module = $module;
    }

    public function createFeedUrl(CalendarFeed $feed){
        $pageUrl    = $this->module->getUrl(UrlBuilder::DEFAULT_PAGE);
        $pageUri    = Uri::createFromString($pageUrl);

        $params = [
            ["action", UrlBuilder::DEFAULT_ACTION],
            ["feed", $feed->key]
        ];

        $queryString  = QueryString::build($params);

        return UriModifier::appendQuery($pageUri, $queryString);
    }

    public function createLinkUrl(CalendarLink $link){
        // $page       = ($link->access_level == "public") ? "public.php" : UrlBuilder::DEFAULT_PAGE;
        $pageUrl    = $this->module->getUrl("public.php", true, true);
        $pageUri    = Uri::createFromString($pageUrl);
        
        $params = [
            ["key", $link->key]
        ];

        $queryString  = QueryString::build($params);

        return UriModifier::appendQuery($pageUri, $queryString);
    }

    public function createExportUrl(CalendarRequest $request, string $format = "ical") : string{
        $pageUrl    = $this->module->getUrl(UrlBuilder::DEFAULT_PAGE);
        $pageUri    = Uri::createFromString($pageUrl);
        
        $params = [
            ["action", "export"],
            ["feed", $request->feed],
            ["format", $format]
        ];

        $filter = $request->filter;
        if (count($filter->events) > 0){
            foreach($filter->events as $event){
                $params[] = ['events[]', $event];
            }
        }

        if (count($filter->records) > 0){
            $params[] = ['records', implode(',', $filter->records)];
        }

        if ($filter->status > 0){
            $params[] = ['status', $filter->status];
        }

        if (is_numeric($filter->month)){
            $params[] = ['month', $filter->month];
        }

        if (is_numeric($filter->year)){
            $params[] = ['year', $filter->year];
        }
            
        //     if (count($filter->getCustomFields()) > 0){
        //     	foreach ($filter->getCustomFields() as $name => $value){
        //     		if (strlen($value) > 0){
        //     			$url = $url."&$name=".$value;
        //     		}
        //     	}
        //     }

        $queryString    = QueryString::build($params);
        $fullUri        = UriModifier::appendQuery($pageUri, $queryString);

        return $fullUri;
    }
}