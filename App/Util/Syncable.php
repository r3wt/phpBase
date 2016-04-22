<?php

namespace App\Util;

/*
	- the purpose of this interface is to define a set of functions an Model must implement in order to:
	- be serialized into a session
	- be synced to database/cache
	- be synced from database/cache
*/

interface Syncable
{
	public function save(); //register object as session
	public function push(); //save changes to cache/database
	public function pull($force); //save changes from cache/database to local object
}