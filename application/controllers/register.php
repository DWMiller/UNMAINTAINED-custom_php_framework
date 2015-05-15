<?php 

/**
 * 
 */
 class Register extends Core_Controller {

	function __construct() {
		  parent::__construct();
	}

	public function index () {
		$args = func_get_args();
		$args = $args[0];

		$result =  Sys_Users_Model::createUser($args['email'],$args['password']);

		if(!$result) {
			
		}
		
		$this->done();
	}

	private function done() {
		if (count($this->errors) > 0) {
			$this->TPL['register-failure'] = $this->errors;
		} else {
			$this->TPL['register-success'] = true;
		}

		$this->output->json_response($this->TPL);
	}



 } 



