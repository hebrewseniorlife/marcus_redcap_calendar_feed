<?php

namespace Controllers;

use ModelFactory as ModelFactory;
use CalendarService as CalendarService;
use CalendarFeedWriter as CalendarFeedWriter;
use UrlBuilder as UrlBuilder;
use SystemDate as SystemDate;
use TemplateEngine as TemplateEngine;

use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpFoundation\HeaderUtils as HeaderUtils;

/**
 * AppController - Kernel for handling HTTP requests via standard REDCap External Module route
 */
class AppController {
    protected $module;
    protected $template;
    protected $factory;

    function __construct(object $module){
        $this->module   = $module;
        $this->template = new TemplateEngine($module->getModulePath()."app/resources/templates/");
        $this->factory  = new ModelFactory($module);
    }
    
    /**
     * createContext
     *
     * @param  mixed $name
     * @param  mixed $additional
     * @return void
     */
    function createContext(string $name, array $additional = []){
        $context = array(
            "module" => array(
                "name"      => $name,
                "prefix"    => $_GET["prefix"],
                "pid"       => $_GET["pid"]
            ),
            "paths" => array(
                "public"    => $this->module->getUrl('public/'),
                "css"       => $this->module->getUrl('public/css'),
                "scripts"   => $this->module->getUrl('public/scripts'),
                "current"   => $_SERVER['REQUEST_URI'],
                "module"    => APP_PATH_WEBROOT.'ExternalModules/?prefix='.$_GET["prefix"],
                "redcap"    => APP_PATH_WEBROOT
            ),
            "months" => SystemDate::getMonths(),
            "years"  => SystemDate::getYearRange()            
        );

        return array_merge($context, $additional);
    }
    
    /**
     * view
     *
     * @param  mixed $request
     * @param  mixed $response
     * @return Response
     */
    function view(Request $request, Response $response) : Response { 
        $calendarRequest = $this->factory->createCalendarRequestByQuery($request);

        $service = new CalendarService($this->module);
        $calendar = $service->getFormattedCalendar($calendarRequest);

        $urlBuilder = new UrlBuilder($this->module);

        $context = $this->createContext("Viewer", [
            "project"   => $calendarRequest->project,
            "feed"      => $calendarRequest->feed,
            "filter"    => $calendarRequest->filter,
            "calendar"  => $calendar,
            "links"     => [
                "ical"  => $urlBuilder->createExportUrl($calendarRequest, "ical"),
                "json"  => $urlBuilder->createExportUrl($calendarRequest, "json"),
                "csv"   => $urlBuilder->createExportUrl($calendarRequest, "csv")
            ],
        ]);

        $content = $this->template->render("view.twig", $context);
        
        $response->setContent($content);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }
    
    /**
     * export
     *
     * @param  mixed $request
     * @param  mixed $response
     * @return Response
     */
    function export(Request $request, Response $response) : Response {
        $calendarRequest = $this->factory->createCalendarRequestByQuery($request);

        $service = new CalendarService($this->module);
        $calendar = $service->getFormattedCalendar($calendarRequest);

        $writer = new CalendarFeedWriter();
        
        $content        = "";
        $filename       = null;
        $contentType    = "text/plain";

        switch($calendarRequest->format){
            case "json":
                $content        = json_encode($calendar->items);
                $filename       = null;
                $contentType    = "application/json";          
            break;      
            case "ical":
                $content        = $writer->createICal($calendar, $calendarRequest->project);
                $filename       = "feed.ical";
                $contentType    = "text/calendar";             
            break;
            case "csv":
            default:
                $content        = $writer->createCsv($calendar);
                $filename       = "feed.csv";
                $contentType    = "text/csv";
            break;
        }

        $response->setContent($content);
        $response->headers->set('Content-Type', $contentType);

        if ($filename != null){
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $filename
            );
            $response->headers->set('Content-Disposition', $disposition);
        }

        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    // function error(Request $request, Response $response, \Exception $error, string $template = "error.twig") : Response {

    //     $previous = [];
    //     if ($error->getPrevious() != null){
    //         $previous = array(
    //             'message' => $error->getPrevious()->getMessage(),
    //             'text' => $error->getPrevious()."" 
    //         );
    //     }

    //     $error = array(
    //         'message' => $error->getMessage(),
    //         'code' => $error->getCode(),
    //         'file' => $error->getFile(),
    //         'line' => $error->getLine(),
    //         'trace' => $error->getTrace(),
    //         'previous' => $previous
    //     );

    //     $error["json"] = \json_encode($error);

    //     $context = $this->createContext('Error', array("error" => $error));       
    //     $content = $this->template->render($template, $context);

    //     $response->setContent($content);
    //     $response->setStatusCode(Response::HTTP_OK);

    //     return $response; 
    // }

}