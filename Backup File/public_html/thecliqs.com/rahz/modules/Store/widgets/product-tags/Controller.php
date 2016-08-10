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

class Store_Widget_ProductTagsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->tags = $tags = Engine_Api::_()->getApi('page', 'store')->getProductsTags();

		if (!count($tags)) {
      return $this->setNoRender();
		}
  }
}