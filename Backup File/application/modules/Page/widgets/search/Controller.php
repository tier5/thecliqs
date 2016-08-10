<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_SearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $modules = Engine_Api::_()->getDbTable('content', 'page')->getEnabledExistAddOns($subject->getIdentity());

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $enabledModules = array();
    foreach ($modules as $module) {
      if ($module['name'] == 'rate') {
        $module['name'] = 'pagereview';
      }
      if ($module['name'] == 'store') {
        $module['name'] = 'store_product';
      }
      if ($module['name'] == 'offers') {
        $module['name'] = 'offer';
      }
      $enabledModules[$module['name']] = 1;

      if ($module['name'] == 'hebadge') {
        unset($enabledModules[$module['name']]);
      }
    }

    $this->view->enabledModules = $enabledModules;

    $this->view->labels = array(
      'pagealbum' => 'Photos',
      'pagevideo' => 'Videos',
      'pageblog' => 'Blogs',
      'pagemusic' => 'Music',
      'pagereview' => 'Reviews',
      'pagediscussion' => 'Discussion',
      'pagedocument' => 'Documents',
      'pageevent' => 'Events',
      'store_product' => 'STORE_Products',
      'donation' => 'Donations',
      'offer' => 'Offers'
    );

    $this->getElement()->setTitle('');
  }
}