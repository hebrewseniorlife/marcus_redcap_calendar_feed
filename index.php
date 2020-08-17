<?php

require_once(__DIR__."/app/bootstrap.php");

use Controllers\AppController as AppController;
use REDCap as REDCap;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

$request  = Request::createFromGlobals();
$response = new Response();


$chromeless = $request->query->getBoolean("chromeless", false);

// This externa module is designed to work only on longitudinal projects..
if (REDCap::isLongitudinal()){
    // Initialize the controller
    $controller = new AppController($module);   
    // Based on the action requested, perform the task...
    switch($request->get("action")){
        case 'export':
            $chromeless = true;
            $response   = $controller->export($request, $response);
        break;
        case 'view':
        default:
            $response = $controller->view($request, $response);
        break;                
    }
}    
else{
    $response->setContent("Calendar feeds are supported on longitudinal projects only.");
}

/*
  Future Note:  Should use Response->Send method to reply to the browser.  
  
  Section required as is because REDCap header and footer PHP file do not 
  properly buffer content.  As a result, the require_once must be used. 
*/
if (!$chromeless){
    require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';
    echo $response->getContent();
    require_once APP_PATH_DOCROOT . 'ProjectGeneral/footer.php';
}
else{
    $response->prepare($request);
    $response->send();
}

