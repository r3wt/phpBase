<?php

$config = [

	'debug'=>true,//enable slim debug

	'strict'=>true,//enforce strict error reporting.

	'base_url'=>'',
	
	'database'=>[
		'host'=>'127.0.0.1',
		'name'=>'',
		'user'=>'',
		'pass'=>'',
		'debug'=>false //set to true when encountering a failing query. redbean will print all queries run. the final query in the output will be the failed query.
	],
	
	
	//memcached
	'cache' => [
		'enabled'=>false,
		'servers'=>[
			['127.0.0.1',11211]//we only used 1 server and havent had the need to grow yet, so this is untested with multiple servers.
		]
	],
	
	//twig settings
	'session' => [
		'name'=>'s_id',//name of session
		'expiry'=>1200,//session duration
		'domain'=>'.',//you will get an error with `.` as the domain. this is expected behavior.
		'type'=>'redis', //what type of session storage to use.
		'host'=>'127.0.0.1', //when scaling to multi server install, this will be used to connect to redis or other session store
		'port'=>6379,  //same
		'restore'=>'s_r',//name of session restore cookie
	],
	
	//mail settings
	'smtp' => [
		'user'=>'',
		'pass'=>'',
		'host'=>'',
		'port'=>587,
		'transport'=>'tls'
	],
	
	//twig settings
	'twig' => [
		'path' => __DIR__ . '/../public_html/views/',
		'default_args' => [
		
		]
	],
	
	
	//apiAuth middleware settings
	'apiAuth' => [
		//define which paths are allowed to bypass the csrf token check
		'bypass_auth'=>[
			'/api/v1/initialize_device',
		]
	],
	
	//aws settings
	'aws_args' =>[
		'config'=>[
			'version'=> 'latest',
			'region'=> '',//one limitation is that all yoru buckets must be in same region.
			'credentials' => [
				'key'    => '',
				'secret' => ''
			],
			'debug'=>false		
		],
		'buckets'=>[
		
			'img'=>[
				'name'=>'',//name of the aws bucket
				'public_url'=>'',//public url to access the bucket, cloudfront, cnamed, or otherwise.
			],
			'video'=>[
				'name'=>'',//name of the aws bucket
				'public_url'=>'',//public url to access the bucket, cloudfront, cnamed, or otherwise.
			],
			'zip'=>[
				'name'=>'',//name of the aws bucket
				'public_url'=>'',//public url to access the bucket, cloudfront, cnamed, or otherwise.
			],
			
		]
	],

	//socketio
	'socketio'=>[
		'endpoint'=>'',//eg https://ws.somedomain.com:3000/
		'key'=>''//we used socketio-auth package over tls with our socketio instance and an ssh keypair for pushing updates from php to node.js. as far as im aware, this should be very secure.
	],
	
];