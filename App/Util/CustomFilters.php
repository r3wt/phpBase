<?php

//our custom filters for RedBeanFVM

namespace App\Util;

class CustomFilters
{
	private $app;
	
	function __construct()
	{
		$this->app = \App\App::getInstance();
	}
	
	public function email_check($input)
	{
		$input = $this->app->filter->email($input);
		if($db->exists('user','email',$input)){
			throw new \exception('An account with that email already exists. Please choose another email address.');
		}
		return $input;
	}
	
	public function pass_check($input)
	{
		$post = $this->app->post;
		if(isset($post['cpass']) && !empty($post['cpass'])){
			// do nothing
			// were using short circuiting here to do it one if statement. 
			// wont work the other way around because || will check both cases
			// where as && stops on the first falsey value
		}else{
			throw new \exception('You must confirm your password');
		}
		if($post['cpass'] !== $input){
			throw new \exception('The entered passwords do not match');
		}
		return $this->app->filter->password_hash($input);
	}
	
}