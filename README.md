openlss/lib-session
===========

Abstract library for handling account sessions and cookies

Usage
----
```php

//extending
ld('session');

abstract class StaffSession extends Session {
	public static function requireManager(){
		if(self::get('is_manager')) return true;
		throw new Exception('Permission denied');
	}

	public static function init(){
		self::$config_name		= 'staff';
		self::$session_name		= 'staff_token';
		self::$session_table	= 'staff_session';
		self::$user_primary_key	= 'staff_id';
	}
}

//overrides the parent vars
StaffSession::init();

//check for session
try {
	if(StaffSession::checkLogin()){
		//register session
		$token = StaffSession::getByToken(StaffSession::getTokenFromSession());
		$session = array_merge(Staff::get($token['staff_id']),$token);
		StaffSession::storeSession($session);
		unset($session,$token);
		//set tpl globals (if Tpl is available)
		if(is_callable(array('Tpl','_get'))){
			Tpl::_get()->set(array(
				 'staff_name'		=>	StaffSession::get('name')
				,'staff_lastlogin'	=>	date(Config::get('date','general_format'),StaffSession::get('last_login'))
			));
		}
	} else {
		if(server('REQUEST_URI') != Url::login()) redirect(Url::login());
	}
} catch(Exception $e){
	StaffSession::tokenDestroy(StaffSession::getTokenFromSession());
	StaffSession::destroySession();
	redirect(Url::login());
}
```

Reference
-----

### (bool) Session::isLoggedIn()
Returns true when a valid session exists

### (bool) Session::requireLogin()
Redirects to Url::login() if no session is found

### (bool) Session::checkLogin()
Checks if a session needed to validate a login exists

### (bool) Session::get($var=false)
Returns MDA key from the session registry
When $var is FALSE the entire registry is returned
```php
$staff_id = Session::get('staff_id');
```

### (bool) Session::storeSession($session)
Sets the session registry
Returns TRUE on success FALSE on failure

### (string) Session::getTokenFromSession()
Returns the current session token

### (void) Session::startSession($token)
Stores the token in an actual PHP session

### (void) Session::destorySession()
Destroys the session help in PHP

### (array) Session::getByToken($token)
Return a session by token

### (array) Session::findToken($id,$remote_ip,$user_agent)
  * $id				The identifier of the account
  * $remote_ip		Remote IP address used for session
  * $user_agent		The USER_AGENT field of the remote user
Returns a session record

### (mixed) Session::tokenCheck($token,$remote_ip,$return_token=false)
  * $token			The session token
  * $remote_ip		Remote IP of the session starter
  * $return_token	When FALSE this function returns BOOL otherwise returns the token

### (string) Session::tokenCreate($id,$remote_ip,$user_agent)
  * $id				The identifier of the account
  * $remote_ip		Remote IP address used for session
  * $user_agent		The USER_AGENT field of the remote user
Returns the newly created token

### (string) Session::tokenDestroy($token)
Destroys the given token and returns that token

