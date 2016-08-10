<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 10.03.12
 * Time: 11:57
 * To change this template use File | Settings | File Templates.
 */
class Page_Widget_AdminSettingsMenuController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $active_item = $this->_getParam('active_item', 'page_admin_settings_global');
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('page_admin_settings', array(), $active_item);
    }
}
