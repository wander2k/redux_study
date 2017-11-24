<?php
namespace cli;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\HttpError as HttpError;
use \Exception;

use cli\Util;
/**
 * Guzzle wrapper for some Rover specific calls.
 *
 */
class RoverClient {
    public $client;
	private $authTokenData = null;
	private $defaultPageSize = 500;
	private $email;
	private $pass;
	private $throttle = 500000;
	public $userAgent;
	public function __construct( $roverhost = HFJRSS_ROVER_HOST , $timeout = ROVER_TIMEOUT, 
	$email = HFJRSS_ROVER_USER_EMAIL, $pass = HFJRSS_ROVER_USER_PASSWORD, $accessToken = Null ) {
		$this->client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $roverhost,
			// You can set any number of default request options.
			'timeout'  => $timeout,
		]);
		$this->userAgent = Util::userAgent();
		if (!empty($email)){
			$this->email = $email;
		}
		if (!empty($pass)){
			$this->pass = $pass;
		}
		if (!empty($accessToken)){
			$this->authTokenData = array( 'access_token' => $accessToken );
		}
		if (defined('HFJRSS_ROVER_THROTTLE_MS')){
			// convert milliseconds to microseconds
			$this->throttle = intval(HFJRSS_ROVER_THROTTLE_MS)*1000;
		}
	}
	/**
	 *
	 */
	public function getAll( $url, $param = array() ){
		/*
		 * Setting default page size
		 * TODO: Move to a config file?
		 */
		$param = array_merge( [ 'page_size' => $this->defaultPageSize, 'count' => 'no' ], $param );
		$response = $this->get( $url, $param );
		$set = $response["data"];
		$list = array();
		while (!empty($set)){
			// the low budget way
			foreach( $set as $item ){
				$list[] = $item;
			}
			$nextURL = $response["links"]["next"];
			if (empty($nextURL)){
				break;
			}
			$response = $this->get( $nextURL );
			$set = Util::array_get( $response, "data", array() );
		}
		return $list;
	}
	/**
	 * simple get
	 * note: get does *not* require user auth
	 */
	public function get( $url, $param = array(), $headerOverride = array() ){
		usleep($this->throttle);
		// if a URL contains a query string, we will parse it up and convert it to an array
		// those query params can be overridden by $param
		$queryString = parse_url( $url, PHP_URL_QUERY);
		$urlParam = array();
		parse_str( $queryString, $urlParam);
		$query = array_merge( $urlParam, $param );
		$authHeader = $this->buildAuthHeader();
		$data = [
			"query" => $query,
			"headers" => $authHeader,
			"http_errors" => false
		];
		if (!empty($headerOverride)){
			$data["headers"] = $headerOverride;
		}

		var_dump($data);

		$result = $this->client->request('GET', $url, $data );
		return json_decode($result->getBody(), True);
	}
	/**
	 * Build out auth header using client key/secret
	 */
	public function buildAuthHeader(){
		$timestamp = time();
		$key = HFJRSS_ROVER_CLIENT_ID;
		$secret = HFJRSS_ROVER_CLIENT_SECRET;
		return [
			"User-Agent"    => $this->userAgent,
			"content-type"  => "application/json",
			"Authorization" => "Doorman-SHA256 Credential={$key}",
			//"Authorization" => "Doorman-SHA1 Credential={$key}",
			"Signature"     => $this->makeSig( $key, $secret, $timestamp),
			"Timestamp"     => $timestamp
		];
	}
	/**
	 * Generate oauth sig
	 */
	public function makeSig( $key, $secret, $timestamp ){
		$sign = $key.$secret.$timestamp;
		// normalize
		$tosign = iconv(mb_detect_encoding($sign, mb_detect_order(), true), "UTF-8", $sign);
		return hash("sha256", $tosign);
		//return hash("sha1", $tosign);
	}
	/**
	 * post *must* require user auth!
	 */
	public function post( $url, $body, $params = array() ){
		usleep($this->throttle);
		$this->authTokenData = $this->authTokenData ?: $this->getAuthTokenData( $this->email, $this->pass );
		$this->validateAccessToken( $this->authTokenData );
		$result = $this->client->request('POST', $url, [
			"json" => $body,
			"headers" => $this->buildAuthHeader()
		]);
		return json_decode($result->getBody(), True);
	}
	/**
	 * put *must* require user auth!
	 */
	public function put( $url, $body, $params = array() ){
		usleep($this->throttle);
		$this->authTokenData = $this->authTokenData ?: $this->getAuthTokenData( $this->email, $this->pass );
		$this->validateAccessToken( $this->authTokenData );
		$result = $this->client->request('PUT', $url, [
			"json" => $body,
			"headers" => $this->buildAuthHeader()
		]);
		return json_decode($result->getBody(), True);
	}
	/**
	 * delete *must* require user auth!
	 */
	public function delete( $url, $params = array() ){
		usleep($this->throttle);
		$this->authTokenData = $this->authTokenData ?: $this->getAuthTokenData( $this->email, $this->pass );
		$this->validateAccessToken( $this->authTokenData );
		$result = $this->client->delete( $url, [
			"headers" => $this->buildAuthHeader()
		] );
		return json_decode($result->getBody(), True);
	}
	/**
	 * patch *must* require user auth!
	 * @todo refactor the request methods later
	 */
	public function patch( $url, $body ){
		$this->authTokenData = $this->authTokenData ?: $this->getAuthTokenData( $this->email, $this->pass );
		$this->validateAccessToken( $this->authTokenData );
		$result = $this->client->request('PATCH', $url, [
			"json" => $body,
			"headers" => $this->buildAuthHeader()
		]);
		return json_decode($result->getBody(), True);
	}
	/**
	 * helper function to get UUID based on user's email
	 */
	public function getUserID( $email ){
		$results = $this->get( "/v2/users", [ "email" => $email ] );
		return $results["data"][0]["id"];
	}
	/**
	 * @todo pass client_id and client_secret as fn args
	 */
	public function getAuthTokenData( $email, $pass ){
		// and also the user access token
		$formdata = array(
			array(
				"name" => "client_id",
				"contents" => HFJRSS_ROVER_CLIENT_ID,
			),
			array(
				"name" => "client_secret",
				"contents" => HFJRSS_ROVER_CLIENT_SECRET,
			),
			array(
				"name" => "email",
				"contents" => $email,
			),
			array(
				"name" => "password",
				"contents" => $pass,
			),
			array(
				"name" => "grant_type",
				"contents" => "password",
			),
			array(
				"name" => "scope",
				"contents" => "openid profile roles user",
			),
		);
		$this->email = $email;
		$this->pass = $pass;
		$response = $this->client->request( "POST", "/openid/token", array(
			"multipart" => $formdata,
			"headers" => [ "User-Agent" => $this->userAgent ]
		));
		$this->authTokenData = json_decode($response->getBody(), true);
		return $this->authTokenData;
	}
	/**
	 * obtain site data by brand/locale
	 */
	public function getSite( $brand, $locale ){
		$response = $this->get( "/v2/sites", array(
			"brand.name" => $brand,
			"locale.url_path" => $locale,
		));
		//var_dump($response);
		if ( empty( $response["data"] ) ){
			return [ "id" => null ];
		}

		if ( array_key_exists('meta', $response) && $response["meta"]["count"] > 1) {
			// HOUSTON, WE HAVE A PROBLEM, 2 sites exist
			throw new Exception( "multiple sites with the same brand/locale combo exists!" );
		}
		return $response["data"][0];
		
	}

	/**
	 * 
	 */
	public function getContentBySite( $siteId, $status, $sort, $page){
		$response = $this->get( "/v2/content", array(
			"site" => $siteId,
			"status" => $status,
			"sort" => $sort,
			"page" => $page,
			"page_size" => 10
		));
		//var_dump($response);
		if ( empty( $response) ){
			return [ "id" => null ];
		} else {
			// if ( array_key_exists('meta', $response) && $response["meta"]["count"] > 1) {
			// 	// HOUSTON, WE HAVE A PROBLEM, 2 sites exist
			// 	throw new Exception( "failed to get content!" );
			// }
			return $response;
		}
	}

	/**
	 * 
	 */
	public function getContentBySiteAndDisplayType( $siteId, $status, $displayTypeId, $sort, $page){
		$response = $this->get( "/v2/content", array(
			"site" => $siteId,
			"display_type"=> $displayTypeId,
			"status" => $status,
			"sort" => $sort,
			"page" => $page,
			"page_size" => 10
		));
		//var_dump($response);
		if ( empty( $response) ){
			//return [ "id" => null ];
			throw new Exception( "failed to get content!" );
		} else {
			// if ( array_key_exists('meta', $response) && $response["meta"]["count"] > 1) {
			// 	// HOUSTON, WE HAVE A PROBLEM, 2 sites exist
			// 	throw new Exception( "failed to get content!" );
			// }
			return $response;
		}
	}

	public function getSectionBySite($siteId) {
		$response = $this->get( "/v2/sections", array(
			"site" => $siteId
		));
		//var_dump($response);
		if ( empty( $response) ){
			return [ "id" => null ];
		} else {
			// if ( array_key_exists('meta', $response) && $response["meta"]["count"] > 1) {
			// 	// HOUSTON, WE HAVE A PROBLEM, 2 sites exist
			// 	throw new Exception( "failed to get content!" );
			// }
			return $response;
		}
	}

	public function getDisplayTypes() {
		$response = $this->get( "/v2/displaytypes");
		//var_dump($response);
		if ( empty( $response) ){
			//return [ "id" => null ];
			throw new Exception( "failed to get content!" );
		} else {
			// if ( array_key_exists('meta', $response) && $response["meta"]["count"] > 1) {
			// 	// HOUSTON, WE HAVE A PROBLEM, 2 sites exist
			// throw new Exception( "failed to get content!" );
			// }
			return $response;
		}
	}

	/**
	 * Create a locale map by id
	 */
	public function getLocaleMap(){
		$out = array();
		$locales = $this->getAll( "/v2/locales" );
		foreach ( $locales as $singleLocale ){
			$out[ $singleLocale[ "id" ] ] = $singleLocale[ "url_path" ];
		}
		return $out;
	}
	/**
	 * obtain BU data by brand
	 */
	public function getBUByBrand( $brand ){
		$response = $this->get( "/v2/brands", array(
			"name" => $brand,
		));
		return Util::array_get( $response, 'data.0.business_unit' );
	}
	/**
	 *
	 */
	public function getBrandsByBU( $buabbreviation, $page = 1 ){
		$response = $this->get( "/v2/brands", array(
			"business_unit.abbreviation" => $buabbreviation,
			"page_size" => 500,
			"page" => $page
		));
		return $response["data"];
	}
	/**
	 * set auth token data
	 */
	public function setauthTokenData( $authTokenData ){
		$this->authTokenData = $authTokenData;
	}
	/**
	 * validate the accessToken data, and check if useruser. If not,
	 * throw the error in order to halt process. First checks to see if auth token
	 * is valid, then checks to see if user is superuser.
	 * @param $accessData - associative array containing access_token data
	 */
	public function validateAccessToken( $accessData ){
		$accessToken = $accessData[ "access_token" ];
		$response = $this->client->request( "GET", "/openid/userinfo", array(
			"query" => [ "access_token" => $accessToken ],
			"headers" => [ "User-Agent" => $this->userAgent ],
			"http_errors" => false
		));
		$userInfo = json_decode( $response->getBody(), true );
		if (empty($userInfo)){
			throw new Exception( "User Validation FAILED: Invalid access_token - {$accessToken}" );
		}
		if (!$userInfo["is_superuser"]){
			throw new Exception( "User Validation FAILED: unauthorized user" .  json_encode($userInfo) );
		}
	}
}