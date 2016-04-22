<?php

namespace App\Middleware;

class Auth
{
	public static function apiAuth(\Slim\Route $route)
	{
		$app = \App\App::getInstance();
		$request = $app->request;
		$path = $request->getPath();
		$allowed = $app->config['apiAuth']['bypass_auth'];
		if(!in_array($path,$allowed)){
			
			if($request->isPost()){
				$field = isset($app->post['c']) ? $app->post['c'] : '';
			}
			if($request->isGet()){
				$field = isset($app->get['c']) ? $app->get['c'] : '';
			}
			$field = urldecode($field);
			if( !hash_equals( $field, $app->session['csrf'] ) ){
				$app->halt(403,'Access Denied');
			}
			
		}
	}
	
}