<?php
class Sys_Users_Model extends ORM {

	//DB properties
	public $data = array(
	'id' => NULL,
	'status' => NULL,
	'is_admin' => NULL,
	'email' => NULL,
	'salt' => NULL,
	'password' => NULL);

	//
	public $session;

	public function __construct($userId, $data = NULL){ 
		parent::__construct($userId, $data);
	} 

	/**
	 * Overriding abstract method in ORM
	 * Default means of grabbing database data for an instance of this class
	 * @return mixed[] [description]
	 */
	protected function getData($id) {
		$sql = "SELECT * FROM users WHERE id = ?";
		$stmt = $dbh->prepare($sql);
    	$stmt->execute(array($id));
    	return $stmt->fetch(PDO::FETCH_ASSOC);
	}
		
	/**
	 * [createUser description]
	 * @param  string $email    [description]
	 * @param  string $password [description]
	 * @return int           [description]
	 */
	public static function createUser($email,$password) {
		$dbh = static::getDatabaseHandler();

		$salt = Hasher::getSalt();
		$password = Hasher::getHashedPassword($salt,$password);	

		$sql = 'INSERT INTO users (email,salt,password) values (?,?,?)';
		$stmt = $dbh->prepare($sql);

		$stmt->execute(array($email,$salt,$password));

		$userID = $dbh->lastInsertId(); 

		return $userID;
	}

	public static function getUserByField($val,$field = 'id') {		
		$dbh = static::getDatabaseHandler();

		$sql = "SELECT * FROM users WHERE $field = ?";
		$stmt = $dbh->prepare($sql);
    
    	$stmt->execute(array($val));

    	$user = false;
    	
		if ($stmt->rowCount() < 1) {	
			return false;	
		}

		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		return new Sys_Users_Model($user['id'],$user);
	}

	/**
	 * [getUserBySession description]
	 * @param  [type] $sessionId [description]
	 * @return Object|false Returns an instance of this class
	 */
	public static function getUserBySession($sessionId) {	
		$dbh = static::getDatabaseHandler();	

		$sql = "SELECT u.* FROM users u
		LEFT JOIN active_sessions a ON a.user_id = u.id
		WHERE a.session = ?";

		$stmt = $this->dbh->prepare($sql);

    	$stmt->execute(array($sessionId));

    	$user = false;
    	
		if ($stmt->rowCount() < 1) {	
			return false;	
		}

		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		return new Sys_Users_Model($user['id'],$user);
	}


}