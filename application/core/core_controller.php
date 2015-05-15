<?php


class Core_Controller extends Controller {

	/**
	 * An instance of the framework Auth class
	 * @var Object
	 */
	protected $Auth;

	/** @var string[] An array to store error messages  */
	protected $errors;


	public function __construct(){ 
		 parent::__construct();
		 $this->Auth = new auth(); 
		 $this->Auth->loggedIn();
		 $this->errors = array();
	 } 

	protected function addError($strErrorTitle, $strErrorMsg) {
		array_push($this->errors, array('title' => $strErrorTitle, 'msg'=> $strErrorMsg));
	} 

	protected function requireAuthentication() {
		if(!$this->User) {
			$this->TPL['no-access'] = true;
			$this->output->json_response($this->TPL);
		}
	}

	protected function requireAdmin() {
		if(!$this->User['is_admin']) {
			$this->TPL['no-access'] = true;
			$this->output->json_response($this->TPL);
		}
	}

}


