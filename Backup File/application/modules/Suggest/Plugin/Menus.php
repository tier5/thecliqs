<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Plugin_Menus
{
  public function getLink()
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()){
      return false;
    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile')) {
      if (Engine_Api::_()->mobile()->isMobileMode()) {
        $subject = Engine_Api::_()->core()->getSubject();
        $suggest_type = 'link_'.$subject->getType();

        if (Engine_Api::_()->suggest()->isAllowed($suggest_type) && Engine_Api::_()->user()->getViewer()->getIdentity()) {
          $router = Zend_Controller_Front::getInstance()->getRouter();
          $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Mobile" . DIRECTORY_SEPARATOR .
            "modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";

          $paramStr = '?m=suggest&l=getSuggestItems&nli=0&params[object_type]='.$subject->getType().'&params[object_id]='.$subject->getIdentity() .
            '&action_url='.urlencode($router->assemble(array('action' => 'suggest'), 'suggest_general')) .
            '&params[suggest_type]=' . $suggest_type . '&params[scriptpath]=' . $path;

          $url = $router->assemble(array('controller' => 'index', 'action' => 'contacts', 'module' => 'hecore'), 'default', true) . $paramStr;
          return array(
            'label' => 'Suggest To Friends',
            'icon' => 'application/modules/Suggest/externals/images/suggest.png',
            'class' => 'suggest_link',
            'uri' => $url
          );
        } else {
          return false;
        }
      }
    }
    
    return array(
      'label' => 'Suggest To Friends',
      'icon' => 'application/modules/Suggest/externals/images/suggest.png',
      'route' => 'suggest_general',
      'class' => 'suggest_link'
    );
  }

  public function onMenuInitialize_UserProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.user');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->isSelf($viewer)) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_EventProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.event');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_GroupProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.group');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_PageProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.page');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_BlogGutterSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.blog');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return array(
      'label' => 'Suggest To Friends',
      'icon' => 'application/modules/Suggest/externals/images/suggest.png',
      'route' => 'suggest_general',
      'class' => 'suggest_link buttonlink'
    );
  }

  public function onMenuInitialize_ClassifiedGutterSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.classified');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return array(
      'label' => 'Suggest To Friends',
      'icon' => 'application/modules/Suggest/externals/images/suggest.png',
      'route' => 'suggest_general',
      'class' => 'suggest_link buttonlink'
    );
  }

  public function onMenuInitialize_OfferProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.offer', 1);

    if (!$showLink) {
      return false;
    }

    return array(
      'label' => 'Suggest To Friends',
      'icon' => 'application/modules/Suggest/externals/images/suggest.png',
      'route' => 'suggest_general',
      'class' => 'suggest_link buttonlink'
    );
  }

}