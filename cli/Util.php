<?php
namespace cli;

/**
 *
 *
 */
class Util {

	public static $userAgent;

	/**
	 * helper function to truncate a float down to given decimals
	 * This is to trim off excess decimal fp values as it is unsightly
	 *
	 * @param float   $val - float with potenticial decimal values
	 * @param string  $f   - decimal flag to truncate at
	 *
	 * @return float - new float with decimal tail trimmed
	 */
	public static function truncateFloat( $val, $f="0" ) {
		if ( ( $p = strpos( $val, '.' ) ) !== false ) {
			$val = floatval( substr( $val, 0, $p + 1 + $f ) );
		}
		return $val;
	}

	/**
	 * Read the csv file, generate an array that uses the first row as the
	 * keys to the respective row's column values
	 * @todo move this to a utility function
	 *
	 * @param string  $filePath
	 * @param object $output - symfony console command output object
	 *
	 * @return mixed - returns an array of items to be created, or null if file error exists
	 */
	public static function processCSVFile( $filePath, $output = null ) {
		// catch any additional uft-8 format read problems - usually from excel
		try {
			$out = array();
			$file = fopen( $filePath, 'r' );
			$firstRow = array();
			while ( ( $data = fgetcsv( $file ) ) !== FALSE ) {
				// not interested in empty rows
				if ( empty( $firstRow ) ) {
					// populate the first row
					$firstRow = $data;
				} else if ( count( $firstRow ) != count( $data ) ) {
						// the number of columns in this row does not match the number of header titles
						$datajson = json_encode( $data );
						$output->writeln( "<error>Column count integrity ERROR: {$filePath}</error> {$datajson}" );
				} else {
					$singleRow = array();
					foreach ( $firstRow as $i => $columnName ) {
						// the first row acts as the column names
						$singleRow[$columnName] = $data[$i];
					}
					$out[] = $singleRow;
				}
			}
			fclose( $file );
			return $out;
		} catch( \Exception $e ) {
			$output->writeln( "<error>CSV File READ ERROR: {$filePath}</error> : {$e->getMessage()}" );
			return array();
		}
	}

	/**
	 * Array filter on multi-dimensional arrays
	 * FROM: http://www.php.net/manual/en/function.array-filter.php#87581
	 *
	 * @param $input
	 * @return array
	 */
	public static function array_filter_recursive( $input ) {
		foreach ( $input as &$value ) {
			if ( is_array($value ) ) {
				$value = self::array_filter_recursive( $value );
			}
		}
		return array_filter($input);
	}

	/**
	 * Helper to get key value out of array w/o notices if key-value is not set.
	 *
	 * To get nested array, we use . notation for levels of nested keys, but that can be changed by fourth parameter.
	 * FROM: RAMS
	 *
	 * @param array   $array
	 * @param string  $key
	 * @param mixed   $default
	 * @param string  $nestDelimiter
	 *
	 * @return mixed
	 */
	public static function array_get( $array, $key, $default=NULL, $nestDelimiter='.' ) {
		//bad keys return default.
		if ( is_null( $key ) || ( is_scalar( $key ) === FALSE ) )  {
			return $default;
		}

		if ( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		}

		foreach ( explode( $nestDelimiter, $key ) as $segment ) {
			if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
				return $default;
			}
			$array = $array[ $segment ];
		}
		return $array;
	}

	/**
	 * Helper function to easily set nested keys on an array
	 *
	 * $key = 'metadata.header.type' would set $array['metadata']['header']['type'] = $value
	 *
	 * @param array $array
	 * @param $key
	 * @param $value
	 * @param $nestDelimiter
	 */
	public static function array_set( array &$array, $key, $value, $nestDelimiter='.' ) {
		if ( empty( $key ) )  {
			return;
		}

		$temp = &$array;
		$exploded = explode( $nestDelimiter, $key );

		foreach( $exploded as $newKey ) {
			$temp = &$temp[ $newKey ];
		}
		$temp = $value;
	}

	/**
	 * find the git tag of the current branch
	 */
	public static function gitTag() {
		$tag = trim(exec('git describe --tags'));
		return $tag;

	}

	/**
	 * output the last commit log
	 */
	public static function gitLog() {
		return trim(exec('git log --pretty=format:"[%s]" -1'));
	}

	/**
	 *
	 */
	public static function userAgent() {

		if ( empty(self::$userAgent)){
			$gitTag = self::gitTag();
			$gitLog = self::gitLog();

			self::$userAgent = "HFJ_RSS/{$gitTag}/{$gitLog}";
		}
		return self::$userAgent;
	}
}