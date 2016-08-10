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


class Store_Widget_StoreSliderFeaturedController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') ) {
      return $this->setNoRender();
		}

    $ipp = $this->_getParam('itemCountPerPage', 6);

    $params = array('approved' => 1, 'featured' => 1, 'ipp' => $ipp, 'page' => 1);
    $this->view->pages = $pages = Engine_Api::_()->getApi('page', 'store')->getPaginator($params);

    if (!$pages->getTotalItemCount()){
      return $this->setNoRender();
    }
  }
}