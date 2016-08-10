<?php

class Ynmediaimporter_Plugin_Menus
{

    /**
     * check current viewer can import albums
     */
    public function canImport($row)
    {
        return $row;
    }

    /**
     * check facebook is enabled
     */
    public function canImportFromFacebook($row)
    {
        
        if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.facebook.enable',1) == 0){
            return false;
        }
        
        return $row;
    }

    /**
     * check facebook is enabled
     */
    public function canImportFromPicasa($row)
    {
        if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.picasa.enable',1) == 0){
            return false;
        }
        return $row;
    }

    /**
     * check facebook is enabled
     */
    public function canImportFromFlickr($row)
    {
        if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.flickr.enable',1) == 0){
            return false;
        }
        return $row;
    }
    
    /**
     * check facebook is enabled
     */
    public function canImportFromInstagram($row)
    {
        if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.instagram.enable',1) == 0){
            return false;
        }
        return $row;
    }
    
    /**
     * check facebook is enabled
     */
    
    public function canImportFromYFrog($row)
    {
        if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.yfrog.enable',1) == 0){
            return false;
        }
        return $row;
    }
    
    
    /**
     * check facebook is enabled
     */
    public function canImportFromTwitpic($row)
    {
        if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.twipic.enable',1) == 0){
            return false;
        }
        return $row;
    }
}
