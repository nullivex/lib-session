<?php
ld('staff','staff_session');

if(session_id() != ''){
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
}
