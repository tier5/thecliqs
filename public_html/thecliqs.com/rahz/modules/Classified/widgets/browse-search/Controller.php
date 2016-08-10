<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9893 2013-02-14 00:00:53Z shaun $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    // Prepare form
    $this->view->form = $form = new Classified_Form_Search();
    
    if( !$viewer->getIdentity() || $p['action'] !== 'browse' ) {
      $form->removeElement('show');
    }
    
    // Populate form
    $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
    if( !empty($categories) && is_array($categories) && $form->getElement('category') ) {
      $form->getElement('category')->addMultiOptions($categories);
    }

    // Process form
    if( $form->isValid($p) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = array_filter($values);

    
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    
    // Process options
    $tmp = array();
    foreach( $customFieldValues as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $customFieldValues = $tmp;
    
    // Do the show thingy
    if( @$values['show'] == 2 ) {
      // Get an array of friend ids to pass to getClassifiedsPaginator
      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);
      // Get stuff
      $ids = array();
      foreach( $friends as $friend )
      {
        $ids[] = $friend->user_id;
      }
      //unset($values['show']);
      $values['users'] = $ids;
    }

    // check to see if request is for specific user's listings
    if( ($user_id = $this->_getParam('user_id')) ) {
      $values['user_id'] = $user_id;
    }

    $this->view->assign($values);
  }
}
