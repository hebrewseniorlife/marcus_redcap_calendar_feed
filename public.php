<?php
// define("NOAUTH", true);

require_once(__DIR__."/app/bootstrap.php");

use Controllers\ApiController as ApiController;

use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

$request  = Request::createFromGlobals();
$response = new Response();

$controller = new ApiController($module);

if ($request->get("key") != null){
  $response = $controller->export($request, $response);
}
else{
  $response->setContent('Key not specified'); 
  $response->setStatusCode(Response::HTTP_BAD_REQUEST);
}



/*
  Future Note:  Should use Response->Send method to reply to the browser.  
  
  Section required as is because REDCap header and footer PHP file do not 
  properly buffer content.  As a result, the require_once must be used. 
*/

$response->prepare($request);
$response->send();