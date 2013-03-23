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
