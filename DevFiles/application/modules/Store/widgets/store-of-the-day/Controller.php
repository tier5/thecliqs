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

class Store_Widget_StoreOfTheDayController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') )
		{
      return $this->setNoRender();
		}
		if (null == ($this->view->store = $store = Engine_Api::_()->getApi('page', 'store')->getStoreOfTheDay())){
      return $this->setNoRender();
		};

    $this->view->photo = $store->photo_id==0 ? false : true;
	  $this->view->widget_title = $this->getElement()->getTitle();
	  $this->getElement()->setTitle('');
  }
}