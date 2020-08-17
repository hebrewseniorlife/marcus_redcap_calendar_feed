<?php
// define("NOAUTH", true);

require_once(__DIR__."/app/bootstrap.php");

use Controllers\ApiController as ApiController;
use REDCap as REDCap;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

$request  = Request::createFromGlobals();
$response = new Response();

// This externa module is designed to work only on longitudinal projects..
if (REDCap::isLongitudinal()){
  $controller = new ApiController($module);
  if ($request->get("key") != null){
    $response = $controller->export($request, $response);
  }
  else{
    $response->setContent('Key not specified.'); 
    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
  }
}
else{
  $response->setContent('Calendar feeds are supported on longitudinal projects only.'); 
  $response->setStatusCode(Response::HTTP_BAD_REQUEST);
}

/*
  Sent the HTTP reponse.  No REDCap header/footer expected in public feed response.
*/

$response->prepare($request);
$response->send();