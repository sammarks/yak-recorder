<?php
// FROM: http://pastebin.com/Bjzph6BQ

/**
	 * The YikUI Client class.
	 *
	 * Provides an simple interface to query the API.
	 *
	 * @package YikUI
	 */
class Client {
	/**
	   * The API host.
	   *
	   * @todo
	   *   Choose hostname based on location:
	   *   - us-east-api.yikyakapi.net
	   *   - us-central-api.yikyakapi.net
	   *   - us-west-api.yikyakapi.net
	   *   - asia-api.yikyakapi.net
	   *   - europe-api.yikyakapi.net
	   */
	const HOST = 'https://us-central-api.yikyakapi.net';
	/**
	   * The API base.
	   */
	const BASE = '/api/';
	/**
	   * The app version.
	   */
	const VERSION = '2.2.1';
	/**
	   * The shared secret key.
	   */
	const KEY = 'F7CAFA2F-FE67-4E03-A090-AC7FFF010729';
	/**
	   * Default cURL options.
	   */
	public static $CURL_OPTIONS = array(
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_USERAGENT => 'Yik Yak/2.2.1 (iPhone; iOS 8.1.2; Scale/2.00)',
		CURLINFO_HEADER_OUT => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_VERBOSE => true,
		CURLOPT_HTTPHEADER => array('Proxy-Connection: keep-alive', 'Accept-Language: en;q=1, zh-Hans;q=0.9', 'Connection: keep-alive', 'Accept: */*'),
		CURLOPT_ENCODING => "gzip",
	);
	/**
	   * The user UUID.
	   */
	protected $user;
	/**
	   * The user latitude.
	   */
	protected $latitude;
	/**
	   * The user longitude.
	   */
	protected $longitude;
	/**
	   * The user altitude (meters).
	   */
	protected $altitude;
	/**
	   * The user speed (default: not moving).
	   */
	protected $speed = -1.000000;
	/**
	   * The user course (default: not moving).
	   */
	protected $course = -1.000000;
	/**
	   * The horizontal accuracy value (meters).
	   */
	protected $horizontal_accuracy = 65.000000;
	/**
	   * The vertical accuracy value (meters).
	   */
	protected $vertical_accuracy = 10.000000;
	/**
	   * Sets user (D and position values so they don't have to be constantly
	   * provided.
	   *
	   * @param string $user
	   *   The user UUID.
	   * @param double $latitude
	   *   The user latitude.
	   * @param double $longitude
	   *   The user longitude.
	   * @param double $altitude
	   *   The user altitude.
	   * @param double $speed
	   *   The user speed (optional).
	   * @param double $course
	   *   The user course (optional).
	   */
	public function __construct($user, $latitude, $longitude, $altitude, $speed = NULL, $course = NULL) {
		$this->user = $user;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->altitude = $altitude;
		if (!is_null($speed)) {
			$this->speed = $speed;
		}
		if (!is_null($course)) {
			$this->course = $course;
		}
	}
	/**
	   * Generates a relative URI with HTTP parameters appended.
	   *
	   * @param string $resource
	   *   The portion of the URL following the endpoint (e.g. "getMessages").
	   * @param array $params
	   *   The request parameters.
	   *
	   * @return string
	   *   A URI relative to the API hostname.
	   */
	public static function buildURI($resource, $params = array()) {
		$uri = static::BASE . $resource;
		if (!empty($params)) {
			$uri .= '?' . http_build_query($params);
		}
		return $uri;
	}
	/**
	   * Gets the user UUID.
	   *
	   * @return string
	   *   The user UUID.
	   */
	public function getUser() {
		return $this->user;
	}
	/**
	   * Gets the user latitude.
	   *
	   * @return double
	   *   The user latitude.
	   */
	public function getLatitude() {
		return $this->latitude;
	}
	/**
	   * Gets the user longitude.
	   *
	   * @return double
	   *   The user longitude.
	   */
	public function getLongitude() {
		return $this->longitude;
	}
	/**
	   * Gets the user altitude.
	   *
	   * @return double
	   *   The user altitude.
	   */
	public function getAltitude() {
		return $this->altitude;
	}
	/**
	   * Gets the user speed.
	   *
	   * @return double
	   *   The user speed.
	   */
	public function getSpeed() {
		return $this->speed;
	}
	/**
	   * Gets the user course.
	   *
	   * @return double
	   *   The user course.
	   */
	public function getCourse() {
		return $this->course;
	}
	/**
	   * Gets the horizontal accuracy.
	   *
	   * @return double
	   *   The horizontal accuracy.
	   */
	public function getHorizontalAccuracy() {
		return $this->horizontal_accuracy;
	}
	/**
	   * Gets the vertical accuracy.
	   *
	   * @return double
	   *   The vertical accuracy.
	   */
	public function getVerticalAccuracy() {
		return $this->vertical_accuracy;
	}
	/**
	   * Sets the user UUID.
	   *
	   * @param string $user
	   *   A valid user UUID.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
	/**
	   * Sets the user latitude.
	   *
	   * @param double $latitude
	   *   The new latitude.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
		return $this;
	}
	/**
	   * Sets the user longitude.
	   *
	   * @param double $longitude
	   *   The new longitude.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
		return $this;
	}
	/**
	   * Sets the user altitude.
	   *
	   * @param double $altitude
	   *   The new altitude.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setAltitude($altitude) {
		$this->altitude = $altitude;
		return $this;
	}
	/**
	   * Sets the user speed.
	   *
	   * @param double $speed
	   *   The new speed.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setSpeed($speed) {
		$this->speed = $speed;
		return $this;
	}
	/**
	   * Sets the user course.
	   *
	   * @param double $course
	   *   The new course.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setCourse($course) {
		$this->course = $course;
		return $this;
	}
	/**
	   * Sets the horizontal accuracy.
	   *
	   * @param double $horizontal_accuracy
	   *   The new horizontal accuracy.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setHorizontalAccuracy($horizontal_accuracy) {
		$this->horizontal_accuracy = $horizontal_accuracy;
		return $this;
	}
	/**
	   * Sets the vertical accuracy.
	   *
	   * @param double $vertical_accuracy
	   *   The new vertical accuracy.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setVerticalAccuracy($vertical_accuracy) {
		$this->vertical_accuracy = $vertical_accuracy;
		return $this;
	}
	/**
	   * Sets the accuracy (shorthand for both horizontal and vertical).
	   *
	   * @param double $horizontal_accuracy
	   *   The new horizontal accuracy.
	   * @param double $vertical_accuracy
	   *   The new vertical accuracy.
	   *
	   * @return Client
	   *   The client instance.
	   */
	public function setAccuracy($horizontal_accuracy, $vertical_accuracy) {
		$this->horizontal_accuracy = $horizontal_accuracy;
		$this->vertical_accuracy = $vertical_accuracy;
		return $this;
	}
	/**
	   * Generates a random user UUID.
	   *
	   * @return string
	   *   A version 4 UUID.
	   */
	public static function generateID() {
		//return strtoupper("666BD96C-8615-4D88-9AC4-F11CC2D22587");
		return strtoupper(sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
								  mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF),
								  mt_rand(0, 0xFFFF),
								  mt_rand(0, 0x0FFF) | 0x4000,
								  mt_rand(0, 0x3FFF) | 0x8000,
								  mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF)
								 ));
	}
	
	public static function generateID2() {
		$uid = md5(self::generateID());
		return substr($uid,0,6) . substr($uid,5,strlen($uid)-1);
	}
	/**
	   * Calculates the hash of a request.
	   *
	   * @param string $data
	   *   The request data.
	   * @param string $salt
	   *   The hash salt.
	   *
	   * @return string
	   *   The request hash.
	   */
	public static function hash($data, $salt) {
		return base64_encode(hash_hmac('sha1', $data . $salt, static::KEY, TRUE));
	}
	/**
	   * Performs a GET request against the API.
	   *
	   * @param string $resource
	   *   The portion of the URL following the endpoint (e.g. "getMessages").
	   * @param array $params
	   *   The request parameters.
	   *
	   * @return mixed
	   *   The request result.
	   *
	   * @throws RequestException
	   */
	public static function get($resource, $params = array(), $cookies = array()) {
		$salt = time();
		$hash = static::hash(static::buildURI($resource, $params), $salt);
		$params = $params + array(
			'salt' => $salt,
			'hash' => $hash
		);
		$ch = curl_init();
		
		$cookieString = "";
		$prefix = "";
		foreach ($cookies as $cookieName=>$cookie) {
			$cookieString .= $prefix . $cookieName . ":" . $cookie;
			$prefix = "; ";
		}
		
		curl_setopt_array($ch, static::$CURL_OPTIONS + array(
			CURLOPT_URL => static::HOST . static::buildURI($resource, $params),
			CURLINFO_HEADER_OUT => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_COOKIE => $cookieString
		));
		$result = curl_exec($ch);
		if ($result === FALSE || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			curl_close($ch);
			throw new Exception(sprintf('Request to /%s failed.', $resource));
		}
		curl_close($ch);
		$data = json_decode($result,true);
		if (json_last_error() != JSON_ERROR_NONE) {
			throw new Exception('Failed to decode JSON response.');
		} 
		return $data;
	}
	
	/**
	   * Creates a new user.
	   *
	   * @param $latitude
	   *   The user latitude.
	   * @param $longitude
	   *   The user longitude.
	   * @param $altitude
	   *   The user altitude.
	   * @param double $speed
	   *   The user speed (optional).
	   * @param double $course
	   *   The user course (optional).
	   *
	   * @return Client
	   *   A client instance.
	   */
	public static function registerUser($latitude, $longitude, $altitude, $speed = NULL, $course = NULL) {
		$reflection = new \ReflectionClass(get_called_class());
		$defaults = $reflection->getDefaultProperties();
		$id = static::generateID();
		//$id = static::generateID2();
		$result = static::get('registerUser', array(
			'userID' => $id,
			'lat' => $latitude,
			'long' => $longitude,
			'version' => self::VERSION
		));
		return new self($id, $latitude, $longitude, $altitude, $speed, $course);
	}
	/**
	   * Gets the most recent messages for a given location.
	   *
	   * @param double $latitude
	   *   The latitude to query for (default: the user latitude).
	   * @param double $longitude
	   *   The longitude to query for (default: the user longitude).
	   *
	   * @return array
	   *   An array of message objects.
	   */
	public function getMessages($latitude = NULL, $longitude = NULL) {
		$result = static::get('getMessages', array(
			'userID' => $this->user,
			'lat' => is_null($latitude) ? $this->latitude : $latitude,
			'long' => is_null($longitude) ? $this->longitude: $longitude,
			'userLat' => $this->latitude,
			'userLong' => $this->longitude,
		));
		return $result['messages'];
	}
}


////// START USER CODE //////

$client = Client::registerUser(
  // latitude
  getenv('YAK_LATITUDE'),
  // longitude
  getenv('YAK_LONGITUDE'),
  // altitude
  296.739 // Some random altitude.
);

$messages = $client->getMessages(
  // latitude
  getenv('YAK_LATITUDE'),
  // longitude
  getenv('YAK_LONGITUDE')
);

echo json_encode($messages);
