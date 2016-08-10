<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function init()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_settings');
  }
  
	public function indexAction()
  {
		$pageModuleTbl = Engine_Api::_()->getDbTable('modules', 'page');
		$newModules = $pageModuleTbl->getNewModules();

		$this->view->form = $form = new Page_Form_Admin_Global();

    $pageViewsTbl = Engine_Api::_()->getDbTable('views', 'page');
    $oldRowInfo = $pageViewsTbl->getOldRowsInfo();

    if ($oldRowInfo['count']) {
      $linkHTML = $this->view->htmlLink($this->view->url(array('module' => 'page', 'controller' => 'settings', 'action' => 'upgrade'), 'admin_default', true), $this->view->translate('Upgrade'), array('class' => 'smoothbox'));
      $description = sprintf($this->view->translate('PAGE_Your database has old formatted %s records. You need to upgrade them. Please click %s'), $oldRowInfo['count'], $linkHTML);
      $form->addNotice($description);
    }


    if( Engine_Api::_()->page()->isPrivacyOld() ) {
      $linkHTML = $this->view->htmlLink($this->view->url(array('module' => 'page', 'controller' => 'settings', 'action' => 'upgrade-privacy'), 'admin_default', true), $this->view->translate('Upgrade'), array('class' => 'smoothbox'));
      $description = sprintf($this->view->translate('PAGE_Your database has old formatted %s records. You need to upgrade them. Please click %s'), Engine_Api::_()->page()->isPrivacyOld(), $linkHTML);
      $form->addNotice($description);
    }
  		$i = 0;
		foreach($newModules as $newModule)
		{
			if ($newModule['informed'] == 0)
			{
				switch($newModule['name'])
				{
					case 'pagealbum' :
						$moduleName = 'Page Album';
						break;
          case 'pageblog' :
						$moduleName = 'Page Blog';
						break;
					case 'pagecontact' :
						$moduleName = 'Page Contact';
						break;
					case 'pagediscussion' :
						$moduleName = 'Page Discussion';
						break;
          case 'pagedocument' :
						$moduleName = 'Page Documents';
						break;
					case 'pageevent' :
						$moduleName = 'Page Event';
						break;
          case 'pagefaq' :
						$moduleName = 'Page FAQ';
						break;
					case 'pagemusic' :
						$moduleName = 'Page Music';
						break;
					case 'pagevideo' :
						$moduleName = 'Page Video';
						break;
          case 'rate' :
						$moduleName = 'Rate';
						break;
          case 'store' :
						$moduleName = 'Store';
						break;
					default:
						$moduleName = $newModule['name'];
						break;
				}
				$form->addNotice(Zend_Registry::get('Zend_Translate')->_('New plugin "'. $moduleName . '" has been installed. You can find it in ' . $this->view->htmlLink( $this->view->url(array( 'module' => 'page', 'controller' => 'editor', 'action' => 'index'), 'admin_default'), $this->view->translate('Default Editor Layout'))));
			}
			$i++;
		}

		$settings = Engine_Api::_()->getDbTable('settings', 'core');

		if (!$this->getRequest()->isPost()){
  		return ;
  	}
  	
    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }

  	//$value = $form->getValue('gmap_key');
  	//$settings->setSetting('page.gmapkey', $value);
  	//$form->gmap_key->setValue($value);
  	
  	$value = $form->getValue('browse_page_count');
    $settings->setSetting('page.browse_count', $value);
  	$form->browse_page_count->setValue($value);

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')) {
      $value = $form->getValue('rate_browse_reviews_count',5);
      $settings->setSetting('rate.browse.reviews.count', $value);
      $form->rate_browse_reviews_count->setValue($value);
    }

  	$value = $form->getValue('recent_page_count');
    $settings->setSetting('page.recent_count', $value);
    $form->recent_page_count->setValue($value);
    
    $value = $form->getValue('popular_page_count');
    $settings->setSetting('page.popular_count', $value);
    $form->popular_page_count->setValue($value);
    
    $value = $form->getValue('featured_page_count');
    $settings->setSetting('page.featured_count', $value);
    $form->featured_page_count->setValue($value);

    $value = $form->getValue('sponsored_page_count');
    $settings->setSetting('page.sponsored_count', $value);
    $form->sponsored_page_count->setValue($value);

    $value = $form->getValue('page_abc');
    $settings->setSetting('page.abc', $value);
    $form->page_abc->setValue($value);

    $value = $form->getValue('default_package');
    $settings->setSetting('default.package.enabled', $value);


    $value = $form->getValue('browse_mode');
    $settings->setSetting('page.browse.mode', $value);

    $value = $form->getValue('adv_search_unit');
    $settings->setSetting('page.advsearch.unit', $value);

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    if ($modulesTbl->isModuleEnabled('offers')) {
      $value = $form->getValue('sort_pages_active_offers');
      $settings->setSetting('page.sort.active.offers', $value);
    }

    if ($modulesTbl->isModuleEnabled('communityad')) {
      $value = $form->getValue('communityad');
      $settings->setSetting('page.communityad.enabled', $value);
    }

    if ($modulesTbl->isModuleEnabled('rate')) {
      $value = $form->getValue('browse_reviews_enable');
      $settings->setSetting('rate.browse.reviews.enable', $value);
    }

    $form->addNotice('Your changes have been saved.');
  }

  public function upgradeAction()
  {
    $pageViewsTbl = Engine_Api::_()->getDbTable('views', 'page');

    $pageViewsTbl->upgradeOldRows();

    $oldRowInfo = $pageViewsTbl->getOldRowsInfo();
    $this->view->rowCount = $oldRowInfo['count'];
  }

  public function upgradePrivacyAction()
  {
    /**
     * @var $authTbl Authorization_Model_DbTable_Allow
     */
    $authTbl = Engine_Api::_()->getDbTable('Allow', 'Authorization');
    $auth = Engine_Api::_()->authorization()->context;
    $id = $authTbl

      ->select()
      ->setIntegrityCheck(false)
      ->from($authTbl->info('name'), array('resource_id', 'count' => new Zend_Db_Expr("COUNT(*)")))
      ->where('resource_type = ?', 'page')
      ->where('action = ?', 'posting')
      ->group('resource_id')
      ->query()
      ->fetch()
    ;

    $features = array('album' => 'pagealbum', 'blog' => 'pageblog', 'disc' => 'pagediscussion', 'doc' => 'pagedocument', 'event' =>  'pageevent', 'music' => 'pagemusic', 'video' => 'pagevideo');


    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->getItem('page', $id['resource_id']);

    if( $page ) {
      $team = $page->getTeamList();
      $likes = $page->getLikesList();
      if( $page ) {
        foreach( $features as $key => $feature ) {
          if( $page->isFeatureAllowed($feature) ) {
            $auth->setAllowed($page, $team, $key . '_posting', 1);
            if( $id['count'] > 1 )
              $auth->setAllowed($page, $likes, $key . '_posting', 1);
            if( $id['count'] > 2 )
              $auth->setAllowed($page, 'registered', $key . '_posting', 1);
          }
        }
      }

      $auth->setAllowed($page, $team, 'posting', 0);
      $auth->setAllowed($page, $likes, 'posting', 0);
      $auth->setAllowed($page, 'registered', 'posting', 0);

    }
    $authTbl->getDefaultAdapter()->delete('engine4_authorization_allow', "`resource_type` = 'page' AND `resource_id` = {$id['resource_id']} AND `action` = 'posting'");

    $this->view->rowCount = Engine_Api::_()->page()->isPrivacyOld();
  }
}