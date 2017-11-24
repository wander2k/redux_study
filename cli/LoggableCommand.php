<?php
namespace cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Support\Facades\DB;

/**
 * For ease of expandability, classes that have the
 * LoggableCommand abstraction will have their output be loggable
 */
abstract class LoggableCommand extends Command{

	public $output;
	public $indent;
	public $verbose = false;
	public $logFilename;
	public $optionMap = [];

	/**
	 *
	 */
	protected function configure() {
		$this->addOption(
			'logFilename',
			null,
			InputOption::VALUE_REQUIRED,
			'Writes all output to a designated logfile under storage/logs. logFilename is the name of the file',
			''
		);
		$this->addOption(
			'indent',
			null,
			InputOption::VALUE_REQUIRED,
			'indents all logged messages',
			0
		);
	}


	/**
	 * Override the addOption feature so we can accrue the available options for the logging feature
	 *
	 * @param string  $name        The option name
	 * @param string  $shortcut    The shortcut (can be null)
	 * @param int     $mode        The option mode: One of the InputOption::VALUE_* constants
	 * @param string  $description A description text
	 * @param mixed   $default     The default value (must be null for InputOption::VALUE_NONE)
	 *
	 * @return Command The current instance
	 */
	public function addOption( $name, $shortcut = null, $mode = null, $description = '', $default = null ) {

		$this->optionMap[$name] = array(
			'mode' => $mode,
			'description' => $description,
			'default' => $default,
		);

		return parent::addOption( $name, $shortcut, $mode, $description, $default );

	}


	/**
	 * returns a map of all available options added and their options type
	 *
	 * @return array of options by name:type
	 */
	public function getOptions() {
		return $this->optionMap;
	}


	/**
	 *
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {

		$this->output = $output;

		$this->logFilename = $input->getOption( 'logFilename' );

		$this->indent = $input->getOption( 'indent' );

		// check verbosity
		if ( OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity() ) {
			$this->verbose = true;
		}

	}

	/**
	 * wirte output depending on verbosity
	 * NOTE: all writeln commands are written to a log if the logFilename has been defined
	 *
	 * @param unknown $line  - line of text
	 * @param unknown $force - for whether or not to display
	 */
	public function writeln( $line, $force = false ) {

		$line = str_repeat( '    ', $this->indent ) . $line;

		// if the string contains an error tag, print it out
		$errorFound = strpos( $line, '<error>' ) !== false;

		if ( $this->verbose || $errorFound || $force ) {
			$this->output->writeln( $line );
		} else {
			$this->output->write( "." );
		}

		$this->writeToLog( $line );
	}

	/**
	 * write logs to the db
	 */
	public function writeToLog( $line ) {

		if ( !empty( $this->logFilename ) ){

			// insert log entries
			$row = array(
				$this->logFilename,
				$line,
				(int) round(microtime(true) * 1000)
			);

			DB::insert('insert into logs (name, entry, created_at) values (?, ?, ?)', $row);
		}

	}
}