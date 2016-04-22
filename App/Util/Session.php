<?php

namespace App\Util;

class Session implements \ArrayAccess, \Countable, \IteratorAggregate
{
	use \App\Util\ArraySingleton;
	
	protected function __construct()
	{
		$c = \App\App::getInstance()->config['session'];
		
		if($c['type'] === 'redis'){
			ini_set('session.save_handler', 'redis');
			ini_set('session.timeout',$c['expiry']);
			session_set_cookie_params($c['expiry'],'/',$c['domain'],false,false);
			ini_set('session.save_path', 'tcp://'. $c['host'] .':'. $c['port']);    
		}
		
		session_name($c['name']);
		session_start();
		if($_SESSION == null){
			$_SESSION = [];
		}
		$this->data = &$_SESSION;
	}
	
	public static function cookie($k,$v,$e)
	{
		$p = session_get_cookie_params();
		setcookie($k,$v,$e,'/',$p['domain'],false,false);
	}

	public static function extend()
	{
		$c = \App\App::getInstance()->config;
		$s = $c['session'];
		$id = session_id();
		self::cookie($c['session']['name'],session_id(),time()+$c['session']['expiry']);
	}
}