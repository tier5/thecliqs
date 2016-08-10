<?php
class Ynultimatevideo_Plugin_Job_UploadToChannel extends Core_Plugin_Job_Abstract 
{
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
        if (!$item) {
            $this -> _setState('failed', 'Video item is missing.');
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
    	// Update to encoding status
        $item -> status = 2;
		$item -> save();
    	Engine_Api::_() -> ynultimatevideo() -> uploadVideoToChannel($item);
    }
}
?>