<?php

class Auth {

	/**
	 * Stores user details when a user has been logged in
	 * @var mixed[]
	 */
	public $User;

	public function login($email,$password) {
		//If user has active and valid session, return user data without trying needing to login again
		$this->User = static::loggedIn();
		if ($this->User) {return true;}

	     //check if user account exists
		$this->User = Sys_Users_Model::getUserByField($email,'email');
		if(!$this->User) {return false;}

		// check if entered password matches account password
		if(!$this->passwordCheck($password)) {return false;}		 	

		$this->User->session = Sys_Sessions_Model::createSession($this->User->id);

		return true;
	}

	private function passwordCheck($enteredPassword) {
		$enteredPassword = Hasher::getHashedPassword($this->User->salt,$enteredPassword);	
		return($this->User->password == $enteredPassword);
	}

	public static function loggedIn() {

		if(isset($_REQUEST['session'])) {
			return Sys_Users_Model::getUserBySession($_REQUEST['session']);
		}
		
		// User has been idle too long
		//$this->timeoutCheck();

		//$this->hackerCheck();	

		// Check if admin has frozen account mid-session
		//$this->frozenCheck();

		return false;
	}

	private function timeoutCheck()
	{
		// $now = time();
		// $expiry = 15;

		// if ($_SESSION['auth']['last_active'] < $now-$expiry)
		// {
		// 	$this->redirect($this->error_pages['timeout']);
		// }

		return false;
	}

	private function hackerCheck()
	{
		$currentIP = $this->Users->getIP();
		// if ($_SESSION['auth']['ipAddress'] != $_SERVER['REMOTE_ADDR']) {
		// 	$this->logout();
		// }

		// if ($_SESSION['auth']['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
		// 	//$this->logout();
		// }
		return false;
	}

	public function logout()
	{
		if(isset($_REQUEST['session'])) {
			$this->Users->clearSession($_REQUEST['session']);
			$_SESSION = array();
			// session_destroy();			
		}
	}
}
