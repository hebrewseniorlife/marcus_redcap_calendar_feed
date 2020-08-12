<?php

require_once(__DIR__."/app/bootstrap.php");

use Controllers\AppController as AppController;

use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

$request  = Request::createFromGlobals();
$response = new Response();

$chromeless = ($_REQUEST["chromeless"]);
$controller = new AppController($module);

switch($_REQUEST["action"]){
    case 'export':
        $chromeless = true;
        $response   = $controller->export($request, $response);
    break;
    case 'view':
    default:
        $response = $controller->view($request, $response);
    break;                
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

