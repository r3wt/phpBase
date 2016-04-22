<?php

namespace App\Controller;

class APIv1
{
	use \App\Util\BaseController;
	use \App\Util\Upload;
	
	public function initialize_device()
	{
		return $this->app->csrf();
		//provides the initial csrf token needed for connecting to the api. 
		// a php application wouldn't need it, but mobile and 3rd party clients, like an angular js app, might.
	}
}