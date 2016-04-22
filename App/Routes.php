<?php

$app->get('/',function(){
	echo '<!DOCTYPE html><html><head><meta name="charset" content="UTF-8" /><title>Hi</title></head><body>Hello World</body></html>';
});

$app->group('/api','\App\Middleware\Auth::apiAuth',function() use($app){
	//iterative versioning is probably good for apis. 
	$app->map('/v1/:method',function($method){
		(new \App\Controller\APIv1)->__try($method);//try just wraps any method call in basic exception handling
	})->via('GET','POST');
	
});