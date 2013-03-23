<?php
/**
 *  OpenLSS - Lighter Smarter Simpler
 *
 *	This file is part of OpenLSS.
 *
 *	OpenLSS is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Lesser General Public License as
 *	published by the Free Software Foundation, either version 3 of
 *	the License, or (at your option) any later version.
 *
 *	OpenLSS is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Lesser General Public License for more details.
 *
 *	You should have received a copy of the 
 *	GNU Lesser General Public License along with OpenLSS.
 *	If not, see <http://www.gnu.org/licenses/>.
 */
namespace LSS;
ld('/func/bcrypt','/func/gen');

//err codes
__e(array(
	 1101	=>	'E_SESSION_INVALID_TOKEN'
	,1102	=>	'E_SESSION_NO_SESSION'
));

abstract class Session {

	static $session = false;
	static $config_name = 'session';
	static $session_name = 'session_token';
	static $session_table = 'session';
	static $user_primary_key = 'user_id';
	static $urls_nologin = array();

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
		if(!self::$session) throw new Exception('No session exists',E_SESSION_NO_SESSION);
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
	public static function getByToken($token){
		$token = substr($token,0,32); //workaround for old sha1 token in new 32-byte guid field
		return Db::_get()->fetch(
			'SELECT * FROM '.self::$session_table.' WHERE token=? AND is_active=?'
			,array($token,1)
		);
	}

	public static function findToken($id,$remote_ip,$user_agent){
		return Db::_get()->fetch(
			'SELECT * FROM `'.self::$session_table.'`'
			.'WHERE `'.self::$user_primary_key.'`=? AND remote_ip=? AND user_agent=? AND is_active=?'
			,array($id,$remote_ip,$user_agent,1)
		);
	}

	public static function tokenCheck($token,$remote_ip,$return_token=false){
		$token = $this->get($token);
		if(!$token || $token['remote_ip'] != $remote_ip)
			throw new Exception('Token invalid',E_SESSION_INVALID_TOKEN);
		if(!$return_token) return true;
		return $token;
	}

	public static function tokenCreate($id,$remote_ip,$user_agent){
		//try to reuse an existing token
		$token = self::findToken($id,$remote_ip,$user_agent);
		if($token) return $token['token'];
		//create a new token
		$token = gen_guid();
		$expires = time() + Config::get(self::$config_name,'token_life');
		Db::_get()->insert(
			self::$session_table
			,array(
				 'token'					=> $token
				,self::$user_primary_key	=> $id
				,'remote_ip'				=> $remote_ip
				,'user_agent'				=> $user_agent
				,'expires'					=> $expires
				,'is_active'				=> 1
			)
		);
		return $token;
	}

	public static function tokenDestroy($token){
		Db::_get()->update(self::$session_table,'token',$token,array('is_active'=>0));
		return $token;
	}

}
