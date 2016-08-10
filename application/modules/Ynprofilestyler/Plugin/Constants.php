<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Plugin_Constants
{
	public static $SUPPORTED_TRANSITIONS = array(
		'fade' => 'Fade',
		'crossFade' => 'Cross Fade'			
	);
	
	public static function getExistingImagesMultiOptions()
	{
		$images = new Ynprofilestyler_Model_DbTable_Images;
		$select = $images->select();
		$arr = array('' => 'No Image');

		foreach ($images->fetchAll($select) as $image)
		{
			$arr["{$image->url}"] = '';
		}
		return $arr;
	}
}