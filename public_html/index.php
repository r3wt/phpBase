<?php

define( 'LIB_PATH' ,  __DIR__ . '/../'  );

require_once LIB_PATH . 'vendor/autoload.php';
require_once LIB_PATH . 'App/App.php';
require_once LIB_PATH . 'App/Config.php';

//all of our shit is autoloaded.
\App\App::registerAutoloader();

//creates the singleton config for usage in whatever may need it.
\App\Util\Config::create($config); 

$app = \App\App::getInstance();

//our database library
$app->container->set('db',function(){
	return \App\Util\Database::getInstance();
});

//library used for filtering form data
$app->container->set('filter',function(){
	return \RedBeanFVM\RedBeanFVM::getInstance();
});

//our memcached library
$app->container->set('cache',function(){
	return \App\Util\Cache::getInstance();
});

//our abstraction to sessions
$app->container->set('session',function(){
	return \App\Util\Session::getInstance();
});

//our singleton config object.
$app->container->set('config',function(){
	return \App\Util\Config::getInstance();
});

//publish stuff to socket.io
$app->container->set('realtime',function(){
	return \App\Util\Realtime::getInstance();
});

//send emails
$app->container->set('email',function(){
	return \App\Util\Email::getInstance();
});

//add middleware
$app->add(new \App\Middleware\RememberMe());


$app->hook('slim.before.dispatch',function() use($app){
	//add our custom filters to RedBeanFVM.
	//have to do it here to ensure that session is correct.
	\RedBeanFVM\RedBeanFVM::configure([
		'user_filters'=>'\\App\\Util\\CustomFilters'
	]);
});

$app->hook('slim.after',function() use($app){
	\App\Util\Session::extend();//keepalive user sessions
});

//define routes
require_once LIB_PATH . 'App/Routes.php';

$app->run();	