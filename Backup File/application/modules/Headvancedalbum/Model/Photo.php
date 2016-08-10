<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photo.php 08-02-13 17:32 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Model_Photo extends Album_Model_Photo
{
    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'healbum_extended',
            'reset' => true,
            'controller' => 'photo',
            'action' => 'view',
            'album_id' => $this->album_id,
            'photo_id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }
}
