<?php

abstract class Session {

	static $session = false;
	static $config_name = 'session';
	static $session_name = 'session_token';
	static $session_table = 'session';
	static $user_primary_key = 'user_id';
	
	//------------------------------------
	//Login Session Handling
	//------------------------------------
	public static function isLoggedIn(){
		if(!self::$session || !mda_get(self::$session,'token')) return false;
		return true;
	}
	
	public static function requireLogin(){
		if(!self::isLoggedIn()) redirect(Url::login());
		return true;
	}
	
	public static function checkLogin(){
		if(!session(self::$session_name)) return false;
		return true;
	}
	
	public static function get($var=false){
		if(!self::$session) throw new Exception('No session exists',ERR_SESSION_NO_SESSION);
		if(!$var) return self::$session;
		return mda_get(self::$session,$var);
	}
	
	public static function storeSession($session){
		self::$session = $session; return true;
	}
	
	public static function getTokenFromSession(){
		return session(self::$session_name);
	}
	
	public static function startSession($token){
		session(self::$session_name,$token);
	}
	
	public static function destroySession(){
		session_delete(self::$session_name);
	}

	//------------------------------------
	//Token Handling
	//------------------------------------
	public static function getToken($token){
		return Db::_get()->fetch(
			'SELECT * FROM '.self::$session_table.' WHERE token = ? AND is_active = ?'
			,array($token,1)
		);
	}
	
	public static function findToken($id,$remote_ip,$user_agent){
		return Db::_get()->fetch(
			'SELECT * FROM `'.self::$session_table.'`'
			.'WHERE `'.self::$user_primary_key.'` = ? AND remote_ip = ? AND user_agent = ? AND is_active = ?'
			,array($id,$remote_ip,$user_agent,1)
		);
	}
	
	public static function tokenCheck($token,$remote_ip,$return_token=false){
		$token = $this->get($token);
		if(!$token || $token['remote_ip'] != $remote_ip)
			throw new Exception('Token invalid',ERR_SESSION_INVALID_TOKEN);
		if(!$return_token) return true;
		return $token;
	}
	
	public static function tokenCreate($id,$remote_ip,$user_agent){
		//try to reuse an existing token
		$token = self::findToken($id,$remote_ip,$user_agent);
		if($token) return $token['token'];
		//create a new token
		$token = self::genToken($id,$remote_ip,$user_agent);
		$expires = time() + Config::get(self::$config_name,'token_life');
		Db::_get()->insert(
			self::$session_table
			,array(
				 'token'					=>	$token
				,self::$user_primary_key	=>	$id
				,'remote_ip'				=>	$remote_ip
				,'user_agent'				=>	$user_agent
				,'expires'					=>	$expires
			)
		);
		return $token;
	}
	
	public static function tokenDestroy($token){
		Db::_get()->update(self::$session_table,'token',$token,array('is_active'=>0));
		return $token;
	}
	
	public static function genToken($id,$remote_ip,$user_agent){
		return sha1($id.$remote_ip.$user_agent.microtime(true));
	}
	
}
