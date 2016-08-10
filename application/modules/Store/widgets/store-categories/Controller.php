<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_StoreCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') ) {
      return $this->setNoRender();
		}

		$this->view->categories = $categories = Engine_Api::_()->getApi('page', 'store')->getPopularCategories();

		if (count($categories) < 1){
      return $this->setNoRender();
		}
  }
}