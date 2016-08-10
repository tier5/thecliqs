<?php

class Ynmediaimporter_Plugin_Core
{
    public function onUserLogoutBefore($payload)
    {
        
        if ($payload instanceof User_Model_User)
        {
            Ynmediaimporter::resetAll();
        }

    }
    
    public function onUserLoginAfter($payload)
    {
        if ($payload instanceof User_Model_User)
        {
            Ynmediaimporter::resetAll();
        }

    }
}
