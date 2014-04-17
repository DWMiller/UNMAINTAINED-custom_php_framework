<?php 	

class Home extends Controller {
	
	 function __construct() {
		  parent::__construct();
	}
	
	function index () {
		$this->TPL['dispWelcomeMsg'] = true;
		$this->view->render('home_v',$this->TPL);
	}
}
