<?php

namespace App\Util;

trait ArraySingleton
{
	protected $data = [];
	protected $defaults = [];
	private static $instance = null;
	protected function __construct(){}
	public static function getInstance()
	{
		return (is_null(self::$instance) ? self::$instance = new self() : self::$instance);
	}
	
	public function setDefault($data)
	{
		$this->data = $this->defaults = $data;
	}
	
	public function fresh()
	{
		$this->data = $this->defaults;
	}
	
	public function get($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function __get($key)
	{
		return $this->get($key);
	}
	
	public function __set($key,$value)
	{
		return $this->set($key,$value);
	}
	
	public function all()
	{
		return $this->data;
	}
	
	public function has($key)
	{
		return array_key_exists($key, $this->data);
	}
	
	public function keys()
	{
		return array_keys($this->data);
	}
	
	public function __isset($key)
	{
		return $this->has($key);
	}
	
	public function remove($key)
	{
		unset($this->data[$key]);
	}

	public function __unset($key)
	{
		$this->remove($key);
	}
	
	public function clear()
	{
		$this->data = array();
	}

	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	public function count()
	{
		return count($this->data);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}
}