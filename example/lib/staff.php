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
namespace LSS\Session;

use \LSS\Db;

abstract class Staff {

	public static function fetchAll(){
		return Db::_get()->fetchAll('SELECT * FROM staff WHERE is_active = ?',array(1));
	}

	public static function createParams(){
		return array(
			 'email'		=> ''
			,'name'			=> ''
			,'is_manager'	=> 1
			,'is_active'	=> 1
		);
	}

	public static function fetch($staff_id){
		return Db::_get()->fetch(
			'SELECT * FROM staff WHERE staff_id=? AND is_active=?'
			,array($staff_id,1)
			,'Staff member could not be found: '.$staff_id
		);
	}

	public static function fetchByEmail($email){
		return Db::_get()->fetch(
			'SELECT * FROM staff WHERE email=? AND is_active=?'
			,array($email,1)
		);
	}

	public static function create($data){
		return Db::_get()->insert(
			'staff'
			,array(
				 'email'		=> mda_get($data,'email')
				,'password'		=> bcrypt(mda_get($data,'password'))
				,'name'			=> mda_get($data,'name')
				,'is_manager'	=> (mda_get($data,'is_manager') ? 1 : 0)
			)
		);
	}

	public static function update($staff_id,$data){
		$update = array(
			 'email'		=> mda_get($data,'email')
			,'name'			=> mda_get($data,'name')
			,'is_manager'	=> (mda_get($data,'is_manager') ? 1 : 0)
		);
		if(mda_get($data['password'])) $update['password'] = bcrypt(mda_get($data,'password'));
		return Db::_get()->update('staff','staff_id',$staff_id,$update);
	}

	public static function updateLastLogin($staff_id){
		return Db::_get()->update('staff','staff_id',$staff_id,array('last_login'=>time()));
	}

	public static function deactivate($staff_id){
		return Db::_get()->update('staff','staff_id',$staff_id,array('is_active'=>0));
	}

	public static function delete($staff_id){
		return Db::_get()->run('DELETE FROM staff WHERE staff_id=?',array($staff_id));
	}

	public static function drop($value=null,$name='staff_id'){
		foreach(self::fetchAll() as $staff)
			$arr[$staff['staff_id']] = $staff['name'].' <'.$staff['email'].'>';
		$drop = \LSS\Form\Drop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}

}
