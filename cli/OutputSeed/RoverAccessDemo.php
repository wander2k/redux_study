<?php
namespace cli\OutputSeed;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GuzzleHttp\Exception\ClientException;

use cli\RoverClient;
use cli\LoggableCommand;

use cli\Util;

/**
 *
 */
class RoverAccessDemo extends LoggableCommand {

	/**
	 *
	 */
	protected $name = 'base';

	protected $description = 'get all data from rover';

	/**
	 *
	 */
	public $rover;

	/**
	 * Path to endpoint related to the seed
	 */
	protected $endpoint;

	/**
	 *
	 */
	public $brand;

	/**
	 *
	 */
	public $locale;

	/**
	 *
	 */
	public $site;

	/**
	 *
	 */
	public $update;

	public $ignorenull;

	/**
	 * @var array of fields with a uniqueness constraint for the specified endpoint
	 */
	public $uniqueFields = [];

	/**
	 *
	 */
	protected function configure() {
		$this
		->setName( $this->name )
		->setDescription( $this->description )
		->addOption(
			'rover_host',
			null,
			InputOption::VALUE_REQUIRED,
			'host where rover is',
			HFJRSS_ROVER_HOST
		)
		->addOption(
			'rover_timeout',
			null,
			InputOption::VALUE_REQUIRED,
			'rover timeout',
			ROVER_TIMEOUT
		)
		->addOption(
			'rover_email',
			null,
			InputOption::VALUE_OPTIONAL,
			'rover account email',
			HFJRSS_ROVER_USER_EMAIL
		)
		->addOption(
			'rover_password',
			null,
			InputOption::VALUE_OPTIONAL,
			'rover account password',
			HFJRSS_ROVER_USER_PASSWORD
		)
		->addOption(
			'nurserydir',
			null,
			InputOption::VALUE_REQUIRED,
			'full path of the nursery directory',
			NURSERY_DIR
		)
		->addOption(
			'dryrun',
			null,
			InputOption::VALUE_NONE,
			'If set, all the motions happen except for the actual writing'
		)
		->addOption(
			'brand',
			null,
			InputOption::VALUE_REQUIRED,
			'the name of the brand this will be saved under',
			'Elle'
		)
		->addOption(
			'locale',
			null,
			InputOption::VALUE_REQUIRED,
			'the locale code this will be saved under',
			'jp'
		)
		->addOption(
			'hardreset',
			null,
			InputOption::VALUE_NONE,
			'If set, will wipe out the data and create a brand new set (WARNING, THIS WILL DESTROY DATA)'
		)
		->addOption(
			'update',
			null,
			InputOption::VALUE_NONE,
			'If set, will only update exiting matching rows based on common identifier'
		)
		->addOption(
			'ignorenull',
			null,
			InputOption::VALUE_NONE,
			'If set, empty values will be removed before saving upstream'
		)
		->addOption(
			'accessToken',
			null,
			InputOption::VALUE_OPTIONAL,
			'oauth access token',
			null
		)
		->addOption(
			'type',
			null,
			InputOption::VALUE_REQUIRED,
			'Test rover target api type(contents/displayTypes)',
			'contents'
		)
		;
		parent::configure();
	}

	/**
	 *
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {
		parent::execute( $input, $output );

		$this->writeLn("execution for OutputTest.");

		$this->rover = new RoverClient( 
			$input->getOption( 'rover_host' ),
			$input->getOption( 'rover_timeout' ),
			$input->getOption( 'rover_email' ),
			$input->getOption( 'rover_password' ),
			$input->getOption( 'accessToken' )
		);

		// get site defaults if found
		$this->brand      = $input->getOption( 'brand' );
		$this->locale     = $input->getOption( 'locale' );

		$this->site = $this->rover->getSite( $this->brand, $this->locale );

		$results = print_r($this->site, true);

		$initialized = $this->initializeSource( $input, $output );

		//var_dump($initialized);
		if ($initialized){
			$startProcessTime = microtime( true );

			$type = $input->getOption( 'type' );
			if ($type == "contents") {
				$this->getContentsAndOutputConsole($input);
			} else if($type == "displayTypes") {
				$this->getDisplayTypes($input);
			}

			$endProcessTime = microtime( true );
			
			$processTimeDuration = Util::truncateFloat( $endProcessTime - $startProcessTime, 2 );
			
			$this->writeln( "<info>{$this->name} : {$type}</info> command complete." );
			$this->writeLn( " <info>Total time: {$processTimeDuration} seconds</info>" );
						
		} else {
			$this->writeln( "<error>Command failed to initialize:</error> {$this->name}" );
		}

	}

	private function getDisplayTypes($input) {
		
		$this->writeLn(" ");	
		$this->writeLn("<info>Here we go!!!Try to get display types from rover!!!</info>");

		$this->writeLn("Connect to server - {$input->getOption( 'rover_host' )}");
		$id = Util::array_get($this->site,"id");
		$this->writeLn("id for elle-jp is : {$id}");

		$contents = $this->rover->getDisplayTypes();
		//var_dump($contents);
		//$this->writeLn(print_r($contents["meta"], true));
		$this->writeLn("Number of contents: " . $contents["meta"]["count"]);
		//var_dump($contents);
		foreach($contents["data"] as $index => $value) {
			$this->writeLn("---------------");
			$cId = Util::array_get($value,"id");
			$title = Util::array_get($value,"title");
			//$media = Util::array_get($value,"media");
			//$mediaPathName = Util::array_get($media,"pathname");
			//var_dump($media);
			$this->writeLn(" No.{$index}:");
			$this->writeLn("       id:{$cId}");
			$this->writeLn("       title:{$title}");
		}

		//$output = print_r($contents);
		//$this->writeln("{$output}");

		$endProcessTime = microtime( true );

	}

	private function getContentsAndOutputConsole($input) {
		$startProcessTime = microtime( true );
		
		$this->writeLn(" ");	
		$this->writeLn("<info>Here we go!!!Try to get content from rover!!!</info>");

		$this->writeLn("Connect to server - {$input->getOption( 'rover_host' )}");
		$id = Util::array_get($this->site,"id");
		$this->writeLn("id for elle-jp is : {$id}");

		$contents = $this->rover->getContentBySite($id, "3", "-updated_at", 1);
		//var_dump($contents);
		//$this->writeLn(print_r($contents["meta"], true));
		$this->writeLn("Number of contents: " . $contents["meta"]["count"]);
		//var_dump($contents);
		foreach($contents["data"] as $index => $value) {
			$this->writeLn("---------------");
			$cId = Util::array_get($value,"id");
			$title = Util::array_get($value,"title");
			$slug = Util::array_get($value,"slug");
			$displayType = Util::array_get($value,"display_type.title");
			$devUrl = Util::array_get($value,"metadata.links.frontend.dev");
			$stage_url = Util::array_get($value,"metadata.links.frontend.stage");
			//$media = Util::array_get($value,"media");
			//$mediaPathName = Util::array_get($media,"pathname");
			//var_dump($media);
			$this->writeLn(" No.{$index}:");
			$this->writeLn("       id:{$cId}");
			$this->writeLn("       title:{$title}");
			//$this->writeLn("       pathname:{$mediaPathName}");
			$this->writeLn("       display_type:{$displayType}");
			$this->writeLn("       dev url:{$devUrl}");
			$this->writeLn("       stage url:{$stage_url}");
			$this->writeLn("---------------");
		}

		//$output = print_r($contents);
		//$this->writeln("{$output}");

	
	}


	/**
	 * placeholder used to do some initial calls before execution
	 *
	 * @param InputInterface   $input - cli input interface object
	 * @param OutputInterface  $output - cli output interface object
     * @return boolean
	 */
	public function initializeSource( InputInterface $input, OutputInterface $output ){ return true; }


	public function getSite() {
		return $this->site;
	}

	public function getRover() {
		return $this->rover;
	}


}