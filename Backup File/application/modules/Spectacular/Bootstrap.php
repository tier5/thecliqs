<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initFrontController() {
        include APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license.php';
    }

}
