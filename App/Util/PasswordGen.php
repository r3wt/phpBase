<?php

namespace App\Util;

class PasswordGen
{
	public static $words = [
		'apple',
		'banana',
		'bicycle',
		'gamer',
		'inspired',
		'spartan',
		'wolf',
		'carnival',
		'airplane',
		'basket',
		'soccer',
		'chess',
		'trophy',
		'orange',
		'clock',
		'innovate',
		'bossman',
		'warrior',
		'thunder',
		'magic',
		'grape',
		'soldier',
		'captain',
		'stereo',
		'plane',
		'shotcaller',
		'touchdown',
		'emoji',
		'surfer',
		'farout',
		'knight',
		'scripting',
		'boosted',
		'uplifting',
		'champion',
		'carrot',
		'potato',
		'legend',
		'sprouted',
		'racecar',
		'minus',
		'level',
		'city',
		'beatbox',
		'church'
	];
	
	public static function generate()
	{
		$k = array_rand(self::$words);
		$str = self::$words[$k];
		$k = mt_rand(2,5);
		$i = 0;
		while($i++ < $k){
			$str .=rand(0,9);
		}
		return $str;
	}
}