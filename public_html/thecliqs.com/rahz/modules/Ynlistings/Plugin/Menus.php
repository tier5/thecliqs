<?php

class Ynlistings_Plugin_Menus {


	public function onMenuInitialize_YnlistingsMainManage() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity())
        {
            return false;
        }
        return true;
    }
    
    public function onMenuInitialize_YnlistingsMainPostListing() {
        if (!Engine_Api::_() -> authorization() -> isAllowed('ynlistings_listing', null, 'create'))
        {
            return false;
        }
        return true;
    }
    
    public function onMenuInitialize_YnlistingsMainImportListing() {
        if (!Engine_Api::_() -> authorization() -> isAllowed('ynlistings_listing', null, 'import'))
        {
            return false;
        }
        return true;
    }
}
