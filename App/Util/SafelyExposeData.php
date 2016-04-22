<?php

namespace App\Util;

interface SafelyExposeData
{
	//should clone self/$this, remove any properties too sensitive to expose over api and return cloned object.
	public function exposeData(); 
}