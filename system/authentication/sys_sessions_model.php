<?php
class Sys_Sessions_Model extends Model {

	public $data = array('id'=>NULL,
	'user_id'=>NULL,
	'ip'=>NULL,
	'session'=>NULL,
	'expires'=>NULL);


	public function __construct($sessionId,$data = NULL){ 
		 parent::__construct($sessionId,$data);
	 } 

	/**
	 * Overriding abstract method in ORM
	 * Default means of grabbing database data for an instance of this class
	 * @return mixed[] [description]
	 */
	protected function getData($id) {
		$sql = "SELECT * FROM active_sessions WHERE id = ?";
		$stmt = $dbh->prepare($sql);
    	$stmt->execute(array($id));
    	return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	private static function getIP() {
		//Test if it is a shared client
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		//Is it a proxy address
		}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		//The value of $ip at this point would look something like: "192.0.34.166"
		return ip2long($ip); //The $ip would now look something like: 1073732954			
	}

	public static function createSession($userId) {
		$dbh = static::getDatabaseHandler();
		$ip = static::getIP();

    	$session = Hasher::getSalt();
    	$expires = date('Y-m-d H:i:s',time()+SESSION_LIMIT);

		$sql = "INSERT INTO active_sessions (user_id,ip,session,expires) values (?,?,?,?)";
		$stmt = $dbh->prepare($sql);
    	$stmt->execute(array($userId,$ip,$session,$expires));

		return $session;
	}

	public static function clearSession($sessionId) {
		$dbh = static::getDatabaseHandler();
		$sql = "DELETE FROM active_sessions WHERE session = ?";
		$stmt = $dbh->prepare($sql);
    	$stmt->execute(array($session));
	}

	public static function clearAllSessions($userId) {
		$dbh = static::getDatabaseHandler();
		$sql = "DELETE FROM active_sessions WHERE user_id = ?";
		$stmt = $dbh->prepare($sql);
    	$stmt->execute(array($userId));
	}

}