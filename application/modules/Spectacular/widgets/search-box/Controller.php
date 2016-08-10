<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Widget_SearchBoxController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $isAdvSearchModEnabled = Engine_Api::_()->hasModuleBootstrap('siteadvsearch');
        if ($isAdvSearchModEnabled) {
            return $this->setNoRender();
        }

        $tableMenusItem = Engine_Api::_()->getDbTable('menuItems', 'core');
        $tableMenusItemName = $tableMenusItem->info('name');
        $menuCartId = $tableMenusItem->select()
                ->from($tableMenusItemName, 'id')
                ->where('name like (?)', '%sitemenu_mini_cart%')
                ->where('enabled =?', 1)
                ->query()
                ->fetchColumn();

        $menuTicketId = $tableMenusItem->select()
                ->from($tableMenusItemName, 'id')
                ->where('name like (?)', '%core_mini_siteeventticketmytickets%')
                ->where('enabled =?', 1)
                ->query()
                ->fetchColumn();
        $this->view->searchbox_width = $this->_getParam('spectacular_search_width', ($menuCartId && $menuTicketId) ? 255 : 275);
    }

}
