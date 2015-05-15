<?php 

/**
 * 
 */
 class Login extends Core_Controller {
	function __construct() {
		parent::__construct();
	}

	function index () {
		$args = func_get_args();
		$args = $args[0];

		$loggedIn = $this->Auth->login($args['email'],$args['password']);

		if(!$loggedIn) {
			$this->addError('Login failed', '');
		}
			
		$this->done();
	}

	function logout () {
		$this->requireAuthentication();	
		$this->Auth->logout($this->User['id']);
		exit;
	}

	function done() {
		if (count($this->errors) > 0) {
			$this->TPL['login-failure'] = $this->errors;
		} else {
			$this->TPL['login-success']['user'] = $this->Auth->User;
		}

		$this->output->json_response($this->TPL);	
	}
 }