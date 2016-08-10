<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Job_Migration extends Core_Plugin_Job_Abstract {
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
        $category_id = $this->getParam('category_id');
        if (!$item || (!($item -> getType() != 'video') && !($item -> getType() != 'ynvideo_playlist'))) {
            $this -> _setState('failed', 'Item is missing.');
            $this -> _setWasIdle();
            return;
        }

        // Process
        try {
            $this -> _process($item, $category_id);
            $this -> _setIsComplete(true);
        }
        catch (Exception $e) {
            $this -> _setState('failed', 'Exception: ' . $e->getMessage());
        }
    }

    protected function _process($item, $category_id)
    {
    	switch ($item->getType()) {
			case 'video':
				Engine_Api::_()->getItemTable('ynultimatevideo_video')->importItem($item, $category_id);
				break;
			
			case 'ynvideo_playlist':
				Engine_Api::_()->getItemTable('ynultimatevideo_playlist')->importItem($item);
			default:
				
				break;
		}
    }

}
