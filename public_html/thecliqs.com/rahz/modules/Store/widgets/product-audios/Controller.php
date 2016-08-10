<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-09-14 17:07:11 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductAudiosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->core()->hasSubject('store_product') ) return $this->setNoRender();

		/**
		 * @var $product Store_Model_Product
		 * @var $audiosTbl Store_Model_DbTable_Audios
		 */
    $product = Engine_Api::_()->core()->getSubject('store_product');
    $this->view->storage = Engine_Api::_()->storage();
    $audiosTbl = Engine_Api::_()->getDbTable('audios', 'store');
    $this->view->audios = $audios = $audiosTbl->getAudios($product->getIdentity());

		if (count($audios) <= 0) {
      return $this->setNoRender();
    }
  }
}