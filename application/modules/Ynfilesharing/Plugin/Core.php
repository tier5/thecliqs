<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
 
class Ynfilesharing_Plugin_Core {
	public function onGroupDeleteBefore($event) {
		$item = $event->getPayload();
		if( $item->getType() == 'group' ) {
			$folders = Engine_Api::_()->ynfilesharing()->getSubFolders(NULL, $item);
			foreach ($folders as $folder) {
				$folder->delete();
			}
		}
	}
	
	public function onUserDeleteBefore($event) {
		$item = $event->getPayload();
		if( $item->getType() == 'user' ) {
			//Remove folders and files in each folders
			$folders = Engine_Api::_()->ynfilesharing()->getSubFolders(NULL, $item);
			if ( count($folders) > 0 ){
				foreach ($folders as $folder) {
					$folder->delete();
				}	
			}
			
			//Remove all files in other users folder
			$files = Engine_Api::_()->ynfilesharing()->getFilesByParent($item);
			if ( count($files) > 0 ){
				foreach ($files as $file) {
					$file->delete();
				}	
			}
			
		}
	}
}