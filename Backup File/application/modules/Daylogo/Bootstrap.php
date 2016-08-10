<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2012-08-16 16:49 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Daylogo_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

    public function __construct($application)
    {
        parent::__construct($application);
        $this->initViewHelperPath();
    }
}