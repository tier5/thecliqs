<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynmusic
 * @author     YouNet Company
 */
class Ynmusic_Plugin_Job_Migration extends Core_Plugin_Job_Abstract {
    protected function _execute() {
        // Get job and params
        $job = $this -> getJob();

        // No video id?
        if (!($item_guid = $this -> getParam('item'))) {
            $this -> _setState('failed', 'No item identity provided.');
            $this -> _setWasIdle();
            return;
        }

        // Get item object
        $item = Engine_Api::_() -> getItemByGuid($item_guid);
        if (!$item || (!($item instanceof Mp3music_Model_Album) && !($item instanceof Mp3music_Model_Playlist) && !($item instanceof Music_Model_Playlist))) {
            $this -> _setState('failed', 'Music item is missing.');
            $this -> _setWasIdle();
            return;
        }

        // Process
        try {
            $this -> _process($item);
            $this -> _setIsComplete(true);
        }
        catch (Exception $e) {
            $this -> _setState('failed', 'Exception: ' . $e->getMessage());
        }
    }

    protected function _process($item) 
    {
    	switch ($item->getType()) {
			case 'mp3music_album':
				Engine_Api::_()->getItemTable('ynmusic_album')->importItem($item);
				break;
			
			case 'mp3music_playlist':
			case 'music_playlist':
				Engine_Api::_()->getItemTable('ynmusic_playlist')->importItem($item);
			default:
				
				break;
		}
    }

}
