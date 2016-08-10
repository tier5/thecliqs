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

class Store_Widget_ProductVideoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->core()->hasSubject('store_product') ) return $this->setNoRender();

		/**
		 * @var $product Store_Model_Product
		 */
    $product = Engine_Api::_()->core()->getSubject('store_product');
    $this->view->video = $video = $product->getVideo();

    if ($video !== null && $video->status) {
      $this->view->videoEmbedded = $video->getRichContent(true);
    } else {
      return $this->setNoRender();
    }

		if (count($video) <= 0) {
      return $this->setNoRender();
    }
  }
}