<?php

class Userauth_m extends Model {

	private $login_page = 'login';   

	private $error_pages = array(
		'access'=>'errorpage/accessViolation',
		'security'=>'errorpage/securityViolation',
		'timeout'=>'errorpage/timeout',
		'frozen'=>'errorpage/frozen'
		);

	private $username;
	private $password;
	private $accesslevel;
	private $freezeaccount;

	private $users;

	public function __construct(){ 
		 parent::__construct();
		  $this->users = new Users_m;
	 } 
		
	public function login()
	{
		//User is already logged in if SESSION variables are good. 
		if ($this->validSessionExists())
		{
			$this->readSession();
		  $this->redirect($this->getDefaultPage());
		}

	  //First time users don't get an error message.... 
	  if ($_SERVER['REQUEST_METHOD'] == 'GET') return;
        
    //Check login form for well formedness.....if bad, send error message
    if (!$this->formHasValidCharacters())
    {
    	return "Invalid characters in form fields! Only letters,numbers, 3-15 chars in length.";
    }

    $this->username = $_POST['username'];
    $this->password = $_POST['password'];
                
    $user = $this->getUserDetails();

    // verify if form's data coresponds to database's data
		if($this->isLoginValid($user['password']))
		{

			$this->accesslevel = $user['accesslevel'];
			$this->freezeaccount = $user['freezeaccount'];

			if ($user['freezeaccount'] == 'Y')
			{
				$this->redirect($this->error_pages['frozen']);
			}

			$this->writeSession();

			$defaultPage = $this->getDefaultPage();

      $this->redirect($defaultPage);
    } else {
    	return 'Invalid username/password. ';
    }
	}

	private function getUserDetails()
	{
		return $this->users->getUserByField($this->username,'username');
	}

	private function isLoginValid($password)
	{
		if($password == $this->password)
		{
			return true;
		}
	}

	public function loggedIn()
	{
	  //Users who are not logged in are redirected out unless page is public 
	  if (!$this->validSessionExists())
		{
			if (!$GLOBALS['config']['acl'][$_REQUEST['c']]['public'])
			{
				$this->redirect($this->error_pages['access']);
			}		
		} 

		// User has been idle too long
		$this->timeoutCheck();

		$this->hackerCheck();	

		$this->updateUserDetails(); // Loads fresh db data for user into session

		// Check if admin has frozen account mid-session
		$this->frozenCheck();

		// User does not have access to view this page
		if (!$GLOBALS['config']['acl'][$_REQUEST['c']][$this->accesslevel])
		{
			$this->redirect($this->error_pages['security']);
		}		
	}

	private function timeoutCheck()
	{
		$now = time();
		$expiry = 15;

		if ($_SESSION['auth']['last_active'] < $now-$expiry)
		{
			$this->redirect($this->error_pages['timeout']);
		}

	}

	private function hackerCheck()
	{
		if ($_SESSION['auth']['ipAddress'] != $_SERVER['REMOTE_ADDR']) {
			$this->logout();
		}

		if ($_SESSION['auth']['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			//$this->logout();
		}
	}

	private function frozenCheck()
	{
		if ($this->freezeaccount == 'Y')
		{
			$this->redirect($this->error_pages['frozen']);
		}
	}

	private function updateUserDetails()
	{
		$user = $this->users->getUserByField($_SESSION['auth']['username'],'username');

		$this->accesslevel = $user['accesslevel'];
		$this->freezeaccount = $user['freezeaccount'];

		$this->updateSession();
	}

	public function logout()
	{
		$_SESSION = array();
		session_destroy();
		$this->redirect('home');
	}
	
	private function getDefaultPage()
	{
		$pages = array(
			'bronze'=>'staff',
			'silver'=>'supervisors',
			'gold'=>'helpdesk',
			'admin'=>'admin',
		);

		return $pages[$this->accesslevel];
	}

	private function redirect($page)
	{
		header("Location: main.php?/".$page); 
		exit(); 		
	}

	public function validSessionExists()
	{
		return isset($_SESSION['auth']['username']);
	}
    
	public function formHasValidCharacters() 
	{
		$exp = '/^[a-z0-9]{3,15}$/i';
		return (preg_match($exp, $_REQUEST['username']) && preg_match($exp, $_REQUEST['password']));
  }

  private function readSession()
  {
			$this->username = $_SESSION['username'];
			$this->password = $_SESSION['password'];
			$this->accesslevel = $_SESSION['accesslevel'];
			$this->freezeaccount = $_SESSION['freezeaccount'];
  }
		  
	private function writeSession() 
	{
			$_SESSION['auth'] = array(
	    'username' => $this->username,
      'ipAddress' => $_SERVER['REMOTE_ADDR'],
      //'client' => $_SERVER['HTTP_USER_AGENT'],
      'accesslevel' => $this->accesslevel,
      'freezeaccount' => $this->freezeaccount,
      'logintime' => time(),
      'last_active' => time()
      );
	}

	private function updateSession()
	{
		$_SESSION['auth']['accesslevel'] = $this->accesslevel;
		$_SESSION['auth']['freezeaccount'] = $this->freezeaccount;
		$_SESSION['auth']['last_active'] = time();		
	}
		
}
