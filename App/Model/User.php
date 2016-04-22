<?php

namespace App\Model;

class User implements \App\Util\SafelyExposeData,\App\Util\Syncable
{
	/* user fields */
	public $id;//user id
	public $token;
	
	protected function __construct($email,$pass = false)
	{
		$this->email = $email;
		$this->pull(true);
		//omitting password bypasses authentication. useful for account registration and restore middleware.
		if($pass){
			if(!password_verify($pass, $this->password)){
				throw new \exception('Invalid Email or Password');
			}
		}
		$this->create_restore();
		$this->create_token();
		$this->save();	
	}
	
	public function exposeData()
	{
		$user = clone $this;
		unset($user->password);
		return $user;
	}
	
	public function save()
	{
		\App\App::getInstance()->session['user'] = $this;
	}
	
	public function pull($force=false)
	{
		$db = \App\App::getInstance()->db;
		$data = $db->cachedCall('getUserByEmail',[$this->email],$force);
		if(empty($data)){
			throw new \exception('Invalid Email or Password');
		}
		foreach($data as $k=>$v){
			$this->{$k} = $v;
		}
	}
	
	public function push()
	{
		$db = (\App\App::getInstance())->db;
		$t = $db->model('user',$this->id);
		$self = get_object_vars($this);
		unset($self['id'],$self['token']);
		foreach($self as $k=>$v){
			$t->{$k} = $v;
		}
		$db->store($t);
	}
	

	
	public function create_token()
	{
		$this->token = sha1(uniqid('',true).time());
	}
	
	public function create_restore()
	{
		$app = \App\App::getInstance();
		$db = $app->db;
		$restore = $db->model('restore');
		$restore->email = $this->email;
		$restore->token = $this->id.'-'.\App\Util\Crypto::uuidV4().md5(time()); //user_id + random id + time
		$db->store($restore);
		\App\Util\Session::cookie($app->config['session']['restore'],$restore->token,time() + (30 * 86400));
	}
	
	public static function register()
	{
		$app = \StableMate\App::getInstance();
		$filter = $app->filter;
		$db = $app->db;
		$post =  $app->post;
		$required = [
			'first_name'=>'name_check',
			'last_name'=>'name_check',
			'email'=>'email_check',
			'password'=>'pass_check'
		];
		
		$t = $db->model('user');
		
		//generate the UAC object first, to ensure there are no problems with email and password.
		$filter->generate_model($t,$required2,[],$post);
		
		$user_id = $db->store($t);
		
		new self($u->email);
	}
	
	public static function login($email,$password)
	{
		if(!$password){
			throw new \exception('You may not login to a user account without the password.');
		}
		new self($email,$password);
	}
	
	public static function restoreSession($token)
	{
		//restore user session
		$app = \App\App::getInstance();
		$success = false;
		$db = $app->db;
		$details = $db->getUserRestoreByToken($token);
		if(!empty($details)){
			try{
				new self($details['email']);
				$db->trash('restore',$details['id']);
				$success = true;
			}
			catch(\exception $e){
				$success = false;
			}
			
		}
		return $success;
	}
	
	public function logout()
	{
		$app = \App\App::getInstance();
		$app->session['user'] = null;
		unset($app->session['user']);
		\App\Util\Session::cookie($app->config['session']['restore'],'blank', time() - 3600 );
	}
	
}