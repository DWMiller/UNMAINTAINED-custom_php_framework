<?php 	

class Home extends Controller {
	
	 function __construct() {
		  parent::__construct();
	}
	
	function index () {
		$args = func_get_args()[0];
		$this->TPL['dispWelcomeMsg'] = true;
		$this->output->json_response($this->TPL);
	}

	function other () {
		$args = func_get_args()[0];
		$this->TPL['dispWelcomeMsg'] = true;
		$this->output->json_response($this->TPL);
	}

}
