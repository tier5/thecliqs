<?php
class Sdtopbarmenu_Widget_TopbarWithoutMainMenuController extends Engine_Content_Widget_Abstract
{
 
  public function indexAction()
  {
	
	$this->view->logo = $this->_getParam('logo');
	
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->navigation2 = $navigation = $this->getNavigation('core_mini');
	
	if( $viewer->getIdentity() )
	{
	  $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'sdtopbarmenu');	
	  $this->view->notificationCount = count($notificationsTable->notificationsOnlys());	
	  $this->view->requestsCount = count($notificationsTable->friendrequestOnlys());
	  $this->view->messageCount = count($notificationsTable->messageOnlys());
	}
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$this->view->notificationOnly = $request->getParam('notificationOnly', false);
	$this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');
  
  
  
  
  //get search box
  $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
	

    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if(!$require_check){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;
  
  
  
  
  
   //sign in form 
   // Do not show if logged in
    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
     // $this->setNoRender();
     // return;
    
    // Display form
    $form = $this->view->form = new User_Form_Login(array(
      'mode' => 'column',
    ));;
    $form->setTitle(null)->setDescription(null);
    $form->removeElement('forgot');

    // Facebook login
    if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
      $form->removeElement('facebook');
    }
    
    // Check for recaptcha - it's too fat
    $this->view->noForm = false;
    if( ($captcha = $form->getElement('captcha')) instanceof Zend_Form_Element_Captcha && 
        $captcha->getCaptcha() instanceof Zend_Captcha_ReCaptcha ) {
      $this->view->noForm = true;
      $form->removeElement('email');
      $form->removeElement('password');
      $form->removeElement('captcha');
      $form->removeElement('submit');
      $form->removeElement('remember');
//      $form->removeElement('facebook');
//      $form->removeElement('twitter');
      $form->removeDisplayGroup('buttons');
    }
  
  }
  
  
  
  
  
  
  
  }

  public function getCacheKey()
  {
    //return Engine_Api::_()->user()->getViewer()->getIdentity();
  }
  
   public function getNavigation($name, array $options = array(), $activeItem = null)
  {
    $pages = $this->getMenuParams($name, $options, $activeItem);
    $navigation = new Zend_Navigation();
    $navigation->addPages($pages);
    return $navigation;
  }

  public function getMenuParams($name, array $options = array(), $activeItem = null)
  {
    $menu = $this->getMenu($name);
    $pages = array();

    foreach( $menu as $row )
    {
      // Check enabled
      if( isset($row->enabled) && !$row->enabled ) {
        continue;
      }

      // Plugin
      $page = null;
      $multi = false;
      if( !empty($row->plugin) ) {

        // Support overriding the method
        if( strpos($row->plugin, '::') !== false ) {
          list($pluginName, $method) = explode('::', $row->plugin);
        } else {
          $pluginName = $row->plugin;
          $method = 'onMenuInitialize_' . $this->_formatMenuName($row->name);
        }

        // Load the plugin
        try {
          $plugin = Engine_Api::_()->loadClass($pluginName);
        } catch( Exception $e ) {
          // Silence exceptions
          continue;
        }

        // Run plugin
        try {
          $result = $plugin->$method($row);
        } catch( Exception $e ) {
          // Silence exceptions
          continue;
        }

        if( $result === true ) {
          // Just generate normally
        } else if( $result === false ) {
          // Don't generate
          continue;
        } else if( is_array($result) ) {
          // We got either page params or multiple page params back
          // Single
          if( array_values($result) !== $result ) {
            $page = $result;
          }
          // Multi
          else
          {
            // We have to do this manually
            foreach( $result as $key => $value )
            {
              if( is_numeric($key) )
              {
                if( !empty($options) )
                {
                  $value = array_merge_recursive($value, $options);
                }
                if( !isset($result['label']) ) $result['label'] = $row->label;
                $pages[] = $value;
              }
            }
            continue;
          }
        } else if( $result instanceof Zend_Db_Table_Row_Abstract && $result->getTable() instanceof Core_Model_DbTable_MenuItems ) {
          // We got the row (or a different row?) back ...
          $row = $result;
        } else {
          // We got a weird data type back
          continue;
        }
      }

      // No page was made, use row
      if( null === $page ) {
        $page = (array) $row->params;
      }

      // Add label
      if( !isset($page['label']) ) {
        $page['label'] = $row->label;
      }

      // Add custom options
      if( !empty($options) ) {
        $page = array_merge_recursive($page, $options);
      }
      
      // Standardize arguments
      if( !isset($page['reset_params']) ) {
        $page['reset_params'] = true;
      }

      // Set page as active, if necessary
      if( !isset($page['active']) && null !== $activeItem && $activeItem == $row->name ) {
        $page['active'] = true;
      }

      $page['class'] = ( !empty($page['class']) ? $page['class'] . ' ' : '' ) . 'menu_' . $name;
      $page['class'] .= " " . $row->name;
      
      // Get submenu
      if( $row->submenu )
      {
        $page['pages'] = $this->getMenuParams($row->submenu);
      }
      
      // Maintain menu item order 
      $page['order'] = $row->order;
      $pages[] = $page;
    }
    return $pages;
  }

  public function getMenu($name)
  {
    // Get only enabled modules
    $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    // Get items
    $table = Engine_Api::_()->getDbtable('menuItems', 'core');
    $select = $table->select()
      ->where('menu = ?', $name)

      ->where('module IN(?)', $enabledModuleNames)
      ->order('order ASC');

    return $table->fetchAll($select);
  }

  protected function _formatMenuName($name)
  {
    $name = str_replace('_', ' ', $name);
    $name = ucwords($name);
    $name = str_replace(' ', '', $name);
    return $name;
  }
}