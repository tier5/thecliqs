<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 07.02.12 12:33 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Widget_BrowseGiftsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */
    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');

    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $page = $this->_getParam('page', 1);
    $category_id = $this->_getParam('category_id', 0);
    $sort = $this->_getParam('sort', 'recent');

    $values = array(
      'sort' => $sort,
      'page' => $page,
      'category_id' => $category_id,
      'ipp' => 20,
      'amount' => true,
      'photo' => true,
      'enabled' => true,
      'status' => 1
    );

    if ($gift = Engine_Api::_()->getItem('gift', $request->getParam('gift_id', 0))) {
      if ($gift->isGeneral()) {
        $this->view->gift_id = $gift->getIdentity();
      }
    }
    $this->view->gifts = $gifts = $table->getGifts($values);
    $this->view->sort = $this->_getParam('sort', 'recent');
    $this->view->count = $gifts->getTotalItemCount();
    $this->view->storage = Engine_Api::_()->storage();
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->user_id = ($request->getParam('user', 0)) ? $request->getParam('user') : $this->_getParam('user_id', 0);
  }
}
