<?php
require_once(__DIR__."/vendor/autoload.php");
require_once __DIR__.'/config/config.php';

require_once( __DIR__ . "/cli/HfjRssApp.php" );

cli\HfjRssApp::get()->run();