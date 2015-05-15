<?php
class Hasher {

	public static function getHashedPassword($salt,$password) {
		return hash('sha384',$salt.$password);  
	}

	public static function getSalt() {
		$salt='';
		for ($i = 0; $i < 40; $i++) { 
		   $salt .= substr(
			 "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", 
			 mt_rand(0, 63), 
			 1); 
		}
		return $salt;
	}
}