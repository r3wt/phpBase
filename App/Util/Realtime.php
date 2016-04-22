<?php

namespace App\Util;

class RealTime
{
	private static $instance = null;
	private $driver = null;
	protected function __construct()
	{
		
	}
	
	public static function getInstance()
	{
		return (is_null(self::$instance) ? self::$instance = new self() : self::$instance);
	}
	
	public function emit($event,$data)
	{
		if(is_null($this->driver)){
			$c = \App\App::getInstance()->config['socketio'];
			$driver = new \ElephantIO\Client(
				new \ElephantIO\Engine\SocketIO\Version1X($c['endpoint'])
			);
			$driver->initialize();
			$driver->emit('authentication',['key'=>$c['key']]);
			$this->driver = $driver;
		}
		$this->driver->emit($event,$data);
		usleep(200);
		return $this;
	}
	
	public function close()
	{
		$this->driver->close();
		$this->driver = null;
	}
}