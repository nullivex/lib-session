<?php

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
