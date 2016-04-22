<?php

namespace App;

class App
{
	private $slim=null;
	private static $instance = null;
	public $files;
	public $get;
	public $post;
	
	protected function __construct()
	{
		//we have to proxy everything to slim that isn't defined here.
		$c = \App\Util\Config::getInstance();
		$cslim = isset($c['slim']) ? $c['slim'] : [];
		$cslim['debug'] = $c['debug'];
		$this->slim = new \Slim\Slim($cslim);
		$this->files   = &$_FILES;
		$this->get     = $this->slim->request()->get();
		$this->post    = $this->slim->request()->post();
		
		if($c['strict']){
			error_reporting( E_ALL | E_NOTICE | E_STRICT );
		}
		
		//add all stuff we need to slims container
	}
	
	
	//proxy calls to slim
	public function __call($fn,$args=[])
	{
		if(method_exists($this->slim,$fn)){
			return call_user_func_array( [$this->slim,$fn] , $args);
		}
		throw new \exception('method doesnt exist::'.$name);
	}
	
	//proxy all sets to slim
	public function __set($k,$v)
	{
		$this->slim->{$k} = $v;
	}
	
	//proxy all gets to slim __get($k)
	public function __get($k)
	{
		return $this->slim->{$k};
	}
	
	public static function getInstance()
	{
		return (is_null(self::$instance) ? self::$instance = new self() : self::$instance);
	}
	
	public static function autoload($class)
	{
		$file =  realpath( __DIR__ . str_replace('\\','/', preg_replace('/'. __NAMESPACE__ .'/','',$class,1)) . '.php');
		if(file_exists($file)){
			require $file;
		}
	}

	public static function registerAutoloader()
	{
		spl_autoload_register(__NAMESPACE__ . "\\App::autoload");
	}
	
	public function csrf()
	{
		if(!isset($this->session['csrf'])){
			$this->session['csrf'] = str_replace('+','',base64_encode(openssl_random_pseudo_bytes(16)));
		}
		return $this->session['csrf'];
	}
	
	public function user()
	{
		return $this->is_user() ? $this->session['user']->exposeData() : false;
	}
	
	public function is_user()
	{
		if( isset( $this->session['user'] ) ) {
			return (bool) ($this->session['user'] instanceof \App\Model\User);
		}else{
			return false;
		}
	}
	
}