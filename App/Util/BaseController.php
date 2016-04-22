<?php

namespace App\Util;

trait BaseController
{
	private $app;
	private $twig;
	private $args;
	
	public function __construct()
	{
		$this->app = \App\App::getInstance();
		$c = $this->app->config['twig'];
		$this->twig = new \Twig_Environment( 
			new \Twig_Loader_Filesystem( $c['path'] )
		);
		$this->twig->addExtension(new \Twig_Extension_StringLoader());
		
		//set default args
		$this->args = array_merge(
			$c['default_args'], //default args from twig config array
			[
				'base_url'=>$this->app->config['base_url'],
				'url'=>$this->app->config['base_url'] . $this->app->request->getPath()
			]
		);
	}
	
	public function __try($fn,$args=[])
	{
		try{
			if(!method_exists($this,$fn)){
				throw new \exception('method not found');
			}else{
				$reflection = new \ReflectionMethod($this, $fn);
				if (!$reflection->isPublic()) {
					throw new \exception('method not found');
				}
			}
			$msg = ['error'=>0,'response'=>call_user_func([$this,$fn])];
		}
		catch(\exception $e){
			if($this->app->config['debug']){
				$err = $e->getMessage().'<br/><hr>'.$e->getFile().' @ Line '.$e->getLine() .'<br/><hr>STACK TRACE:<br/>'.$e->getTraceAsString();
			}else{
				$err = $e->getMessage();
			}
			$msg = ['error'=>1,'response'=>$err];
		}
		$this->json($msg);
	}
	
	private function json($m)
	{
		echo json_encode($m, JSON_HEX_QUOT | JSON_HEX_TAG);
	}
	
	private function render($template,$args,$return=false)
	{
		$html = $this->twig->loadTemplate($template)->render($args);
		if($return) 
			return $html;
		else
			echo $html;
	}
	
}