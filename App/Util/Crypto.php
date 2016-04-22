<?php

namespace App\Util;

class Crypto
{
	public static uuidV4()
	{
		if( version_compare(PHP_VERSION,'7.0.0', '<') ){
			return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',	   
				// 32 bits for "time_low"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			   
				// 16 bits for "time_mid"
				mt_rand(0, 0xffff),
			   
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand(0, 0x0fff) | 0x4000,
			   
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand(0, 0x3fff) | 0x8000,
			   
				// 48 bits for "node"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
			);
		}else{
			return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				random_int(0, 0xffff), random_int(0, 0xffff),
			   
				// 16 bits for "time_mid"
				random_int(0, 0xffff),
			   
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				random_int(0, 0x0fff) | 0x4000,
			   
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				random_int(0, 0x3fff) | 0x8000,
			   
				// 48 bits for "node"
				random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
			);
		}
	}
	
	//converts all arguments to strings, shuffling on each iteration
	//then shuffles and returns the resultant string
	public static function str_shuffle_args()
	{
		$args = func_get_args();
		$o = '';
		foreach($args as $a){
			switch(gettype($a)){
				case 'array':
				case 'object':
					$o.=str_shuffle(serialize($a));
				break;
				case 'integer':
				case 'boolean':
				case 'double':
					$o.=str_shuffle((string)intval($a));
				break;
				case 'string':
					$o.=str_shuffle($a);
				break;
				default:
					//ignore
				break;
			}
		}
		return str_shuffle($o);
	}
	
	public static function crypto_rand_secure($min =0, $max=PHP_INT_MAX)
	{
		$range = $max - $min;
		if ($range < 1) return $min; // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);
		return $min + $rnd;
	}
	
	public static function token($length=32)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet) - 1;
		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[self::crypto_rand_secure(0, $max)];
		}
		return $token;
	}
	
}