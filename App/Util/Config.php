<?php

namespace App\Util;

class Config implements \ArrayAccess, \Countable, \IteratorAggregate
{
	use \App\Util\ArraySingleton;
	/* @void create() */
	public static function create($config)
	{
		static::getInstance()->setDefault($config);
	}
}