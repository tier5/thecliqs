<?php

class Ynbusinesspages_Model_Modulesetting extends Core_Model_Item_Abstract 
{
	public function getActionText($all = 0)
	{
		$str = strtolower($this->title);
		$str = str_replace(' privacy', '', $str);
		if ($all == 1)
		{
			$arr = explode(' ', $str);
			if (count($arr) > 1)
			{
				$str = str_replace('delete ', ' delete all ', $str);
				$str = str_replace('edit ', ' edit all ', $str);
				return $str;
			}
		}
		return $str;
	}
	
}
