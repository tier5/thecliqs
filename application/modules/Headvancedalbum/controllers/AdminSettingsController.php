<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2013-01-17 15:23:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('headvancedalbum_admin_main', array(), 'headvancedalbum_admin_main_settings');

        $form = new Headvancedalbum_Form_Admin_Global();
        $this->view->form = $form;

        if(!$this->getRequest()->isPost()) {
            return;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->setSetting('headvancedalbum.featured.albums.count', $this->_getParam('featured_albums_count', 10));
        $settings->setSetting('headvancedalbum.featured.photos.count', $this->_getParam('featured_photos_count', 10));

        $settings->setSetting('headvancedalbum.popular.albums.count', $this->_getParam('popular_albums_count', 10));

        $form->populate($this->_getAllParams());
    }
}
