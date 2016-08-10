<?php


class Yncontest_Api_Cart extends Core_Api_Abstract
{
	public function getSecurityCode()
	{
		$sid = 'abcdefghiklmnopqstvxuyz0123456789ABCDEFGHIKLMNOPQSTVXUYZ';
		$max =  strlen($sid) - 1;
		$res = "";
		for($i = 0; $i<16; ++$i){
			$res .=  $sid[mt_rand(0, $max)];
		}
		return $res;
	}
}