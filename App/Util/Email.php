<?php

namespace App\Util;

class Email
{
	private static $instance = null;
	
	protected function __construct(){}
	
	public static function getInstance()
	{
		return (is_null(self::$instance) ? self::$instance = new self() : self::$instance);
	}
	
	public function send($subject,$body,$recipient,$html=false)
	{
		$c = \App\App::getInstance()->config['smtp'];
		
		if($c['transport'] === 'ssl'){
			$transport = \Swift_SmtpTransport::newInstance($c['host'], $c['port'],'ssl');
		}
		else if($c['transport'] === 'tls'){
			$transport = \Swift_SmtpTransport::newInstance($c['host'], $c['port'],'tls');
		}else{
			$transport = \Swift_SmtpTransport::newInstance($c['host'], $c['port']);
		}
		
		$transport->setUsername($c['user']);
		$transport->setPassword($c['pass']);
		$swift = \Swift_Mailer::newInstance($transport);
		$message = new \Swift_Message($subject);
		$message->setFrom($c['user']);
		if($html){
			$message->setBody($body, 'text/html');
		}else{
			$message->setBody($body, 'text/plain');
		}
		$message->setTo($recipient);
		if ($recipients = $swift->send($message, $failures))
		{
			return true;
		} else {
			Throw new \exception('Unable to send email');
		}
	}
}