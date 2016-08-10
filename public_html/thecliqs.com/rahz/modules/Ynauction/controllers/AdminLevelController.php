<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminLevelController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;
    
    // Make form
    $form = new Ynauction_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    // Populate data
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('ynauction_product', $id, array_keys($form->getValues())));
    if($id != 5)   
    {
        $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $psselect = $mtable->select()
                    ->where("type = 'ynauction_product'")
                    ->where("level_id = ?",$id)
                    ->where("name = 'publish_fee'");
        $fsselect = $mtable->select()
                    ->where("type = 'ynauction_product'")
                    ->where("level_id = ?",$id)
                    ->where("name = 'feature_fee'");
        $mallow_p = $mtable->fetchRow($psselect);
        $mallow_f = $mtable->fetchRow($fsselect);
       
        if (!empty($mallow_p))
            $max_p = $mallow_p['value'];
        
        if (!empty($mallow_f))
            $max_f = $mallow_f['value'];

        $max_p_get = $form->publish_fee->getValue();
        $max_f_get = $form->feature_fee->getValue();  
         
        if ($max_p_get < 1)
        $form->publish_fee->setValue($max_p);
        if ($max_f_get < 1)
        $form->feature_fee->setValue($max_f);
    }
    $this->view->form = $form;
    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $flag = 0;
    $values = $form->getValues();
    if(isset($values['publish_fee']))
    {
    if(!is_numeric($values['publish_fee']) || $values['publish_fee'] < 0)
      {
                $form->getElement('publish_fee')->addError('The fee number is invalid! (Ex: 5)');
                $flag = 1;
      }
       $values['publish_fee'] = round($values['publish_fee']);    
    }
    if(isset($values['feature_fee']))
    {
       if(!is_numeric($values['feature_fee']) || $values['feature_fee'] < 0)
      {
                $form->getElement('feature_fee')->addError('The fee number is invalid! (Ex: 5)');
                $flag = 1;  
      }
      $values['feature_fee'] = round($values['feature_fee']); 
    }
      if($flag == 1)
        return;
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set permissions
      $permissionsTable->setAllowed('ynauction_product', $id, $values);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

}