<?php
namespace cli;

use cli\OutputSeed\RoverAccessDemo;

use Symfony\Component\Console\Application;

class HfjRssApp {

	public static function get(){


        $application = new Application();
        
        $application->add(new RoverAccessDemo());

		return $application;

	}
}
