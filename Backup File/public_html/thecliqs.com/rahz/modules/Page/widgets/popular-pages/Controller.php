<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-11-08 16:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_PopularPagesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('page.popular_count', 6);

    $params = array('approved' => 1, 'sort' => 'popular', 'ipp' => $ipp, 'p' => 1);
    $this->view->pages = $pages = $table->getPaginator($params);
    
    if (!$pages->getTotalItemCount()){
      return $this->setNoRender();
    }
  }
}