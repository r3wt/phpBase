<?php

namespace App\Middleware;

class RememberMe extends \Slim\MiddleWare
{
	public function __construct(){}
	
	public function call()
	{
		$app = \App\App::getInstance();
		
		//users
		$key = $app->config['session']['restore'];
		if(!$app->is_user() && isset($_COOKIE[$key]) && \App\Model\User::restoreSession($_COOKIE[$key])){
			if(!$app->is_user()){
				$app->session['user'] = $_SESSION['user'];
			}
		}
		
		if($app->is_user()){
			$app->session['user']->pull();//update user session with latest data from cache.
		}
		
		$this->next->call();
	}
}