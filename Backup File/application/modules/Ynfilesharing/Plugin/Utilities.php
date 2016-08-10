<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Plugin_Utilities
{
	public static function findFileExt($filename)
	{
		$filename = strtolower($filename) ;
		//$exts = split("[/\\.]", $filename) ;
		$exts = explode(".", $filename);
		$n = count($exts)-1;
		if ($n > 0) {
			$exts = $exts[$n];
		} else {
			$exts = '';
		}
		return $exts;
	}
	
	public static function findFileName($filename)
	{
// 		$exts = split("[/\\.]", $filename) ;
		$exts = explode(".", $filename);
		
		$n = count($exts)-2;
		if ($n < 0) {
			$n = 0;
		}
		$exts = $exts[$n];
		return $exts;
	}
		
	public static function removeDir($dirname)
	{
	    // Sanity check
	    if (!file_exists($dirname)) {
	        return false;
	    }
	    // Simple delete for a file
	    if (is_file($dirname)) {
	        return unlink($dirname);
	    }
	
	    // Loop through the folder
	    $dir = dir($dirname);
	    while (false !== $entry = $dir->read()) {
	        // Skip pointers
	        if ($entry == '.' || $entry == '..') {
	            continue;
	        }
	        // Recurse
	        self::removeDir($dirname . DIRECTORY_SEPARATOR . $entry);
	    }
	
	    // Clean up
	    $dir->close();
	    return rmdir($dirname);
	} 
	
	public static function getFolderSize($dirname) {
		$folderSize = 0;
        // open the directory, if the script cannot open the directory then return folderSize = 0
        $dir_handle = @opendir($dirname);
        if (!$dir_handle) return 0;

        // traversal for every entry in the directory
        while ($file = readdir($dir_handle)){

            // ignore '.' and '..' directory
            if  ($file  !=  "."  &&  $file  !=  "..")  {

                // if entry is directory then go recursive !
                if  (is_dir($dirname . DIRECTORY_SEPARATOR . $file)){
                          $folderSize += self::getFolderSize($dirname . DIRECTORY_SEPARATOR . $file);

                // if file then accumulate the size
                } else {
                      $folderSize += filesize($dirname . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        // chose the directory
        closedir($dir_handle);

        // return $dirname folder size
        return $folderSize ;
    }
    
	public static function random_gen($length)
	{
	  $random= "";
	  srand((double)microtime()*1000000);
	  $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  $char_list .= "abcdefghijklmnopqrstuvwxyz";
	  $char_list .= "1234567890-_";
	  // Add the special characters to $char_list if needed
	  for($i = 0; $i < $length; $i++)
	  {
	    $random .= substr($char_list,(rand()%(strlen($char_list))), 1);
	  }
	  return $random;
	}
	
	public static function getBaseUrl() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$baseUrl = sprintf ( '%s://%s', $request->getScheme (), $request->getHttpHost () );
		return $baseUrl;
	}
}