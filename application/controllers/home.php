<?php 	

class Home extends Controller {
	
	 function __construct() {
		  parent::__construct();
	}
	
	function index () {
		$this->TPL['dispWelcomeMsg'] = true;
		$this->output->json_response($this->TPL);
	}

	function other () {
		$this->TPL['dispWelcomeMsg'] = true;
		$this->output->json_response($this->TPL);
	}

}
