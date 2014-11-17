<?php
class Core_Controller extends Controller {
	protected $Auth;
	protected $User;

	public function __construct(){ 
		 parent::__construct();
		 $this->Auth = new userauth_m(); 
		 $this->User = $this->Auth->loggedIn();
	 } 
}


