<?php

require_once(__DIR__."/../vendor/autoload.php");
require_once(__DIR__.'/../config/config.php');

date_default_timezone_set('UTC');

use cli\RoverClient;

$rover = new RoverClient( 
    HFJRSS_ROVER_HOST,
    ROVER_TIMEOUT,
    HFJRSS_ROVER_USER_EMAIL,
    HFJRSS_ROVER_USER_PASSWORD,
    ""
);

// elle-jp
$siteId = "2bb3c0e5-8b6f-4968-8aa7-5fdc0868aad8";
// Long Form Article
$displayTypeId = "fb518225-f53e-499f-b254-e0e5f1d66f46";

$contents = $rover->getContentBySiteAndDisplayType($siteId, "3", $displayTypeId, "-updated_at", 1);

header("Content-Type: application/json; charset=utf-8");
$json = json_encode($contents);
echo $json;
//echo json_encode($contents, JSON_UNESCAPED_UNICODE);
//var_dump($rover);
//var_dump($contents);

