<?php 
class Ynfeed_Model_Map extends Core_Model_Item_Abstract
{
	protected $_searchTriggers = false;
	public function getRichContent($view = false, $params = array())
	{
		$mapEmbedded = '';
		if( !$view ) 
		{
			$mapEmbedded .= "<iframe  style='width:100%;' src='";
			$mapEmbedded .= "https://maps.google.com/?q=loc:{$this->latitude},{$this->longitude}&sensor=false&maptype=roadmap&markers=color:blue&&t=m&ie=UTF8&output=embed&iwloc=near'";
			$mapEmbedded .= "></iframe>";
		}
		return $mapEmbedded;
	}
}
