<?php

namespace Controllers;

use ModelFactory as ModelFactory;
use CalendarService as CalendarService;
use CalendarFeedWriter as CalendarFeedWriter;
use ValidationException;
use Exception;

use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpFoundation\HeaderUtils as HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse as JsonResponse;

class ApiController {
    protected $module;
    protected $factory;

    function __construct(object $module){
        $this->module   = $module;
        $this->factory  = new ModelFactory($module);
    }

    function export(Request $request, Response $response) : Response {
        try{
            $calendarRequest = $this->factory->createCalendarRequestByLink($request);
        }
        catch(ValidationException $error){
            return new Response($error->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        catch(Exception $error){
            return $this->error(new Exception('Failed to create the calendar request.', 0 , $error));
        }

        try{
            $service = new CalendarService($this->module);
            $calendar = $service->getFormattedCalendar($calendarRequest);
        }
        catch(Exception $error){
            return $this->error(new Exception('Failed to get a properly-formated calendar.', 0 , $error));
        }

        $writer = new CalendarFeedWriter();
        
        $content        = "";
        $contentType    = "text/plain";

        switch($calendarRequest->format){
            case "json":
                $content        = json_encode($calendar->items);
                $contentType    = "application/json";          
            break;      
            case "ical":
                $content        = $writer->createICal($calendar, $calendarRequest->project);
                $contentType    = "text/calendar";             
            break;
            case "csv":
            default:
                $content        = $writer->createCsv($calendar);
                $contentType    = "text/csv";
            break;
        }

        $response->setContent($content);
        $response->headers->set('Content-Type', $contentType);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    function error(Exception $error, int $statusCode = Response::HTTP_BAD_REQUEST) : Response {

        $previous = [];
        if ($error->getPrevious() != null){
            $previous = [
                'message' => $error->getPrevious()->getMessage(),
                'text' => $error->getPrevious().""
            ];
        }

        $error = [
            'message'   => $error->getMessage(),
            'code'      => $error->getCode(),
            'file'      => $error->getFile(),
            'line'      => $error->getLine(),
            'trace'     => $error->getTrace(),
            'previous'  => $previous
        ];

        return new JsonResponse($error, $statusCode);
    }    
}