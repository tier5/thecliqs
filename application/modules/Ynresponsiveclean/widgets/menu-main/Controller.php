<?php
/**
 * SocialEngine
 *
 * @category   Application_Themes
 * @package    Template
 * @copyright  Copyright YouNet Company
 */
 
class Ynresponsiveclean_Widget_MenuMainController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive1' && 'ynresponsiveclean' != substr(YNRESPONSIVE_ACTIVE, 0, 17))
	{
		return $this -> setNoRender(true);
	}	
    //Logo
    $this->view->logo = $this->_getParam('logo', false);
  	
    $this->view->navigationMain = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_main');
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    
    if(!$require_check && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
  }

  public function getCacheKey()
  {
  }
}