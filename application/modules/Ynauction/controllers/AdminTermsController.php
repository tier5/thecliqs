<?php

class Ynauction_AdminTermsController extends Core_Controller_Action_Admin {

   public function indexAction() {

      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_terms');



      $this->view->form = $form = new Ynauction_Form_Admin_Terms_Terms();

      $table = Engine_Api::_()->getDbTable('statics', 'ynauction');
      $select = $table->select();
      $select->where('static_name = ?', 'terms');
      $row = $table->fetchRow($select);

      if (!$this->getRequest()->isPost()) {

         $form->populate($row->toArray());
         return;
      }


      if (!$form->isValid($this->getRequest()->getPost())) {
         return;
      }


      // Process
      $db = $table->getAdapter();
      $db->beginTransaction();

      if ($this->getRequest()->isPost()) {
         try {
            $term = $row;
            $values = $form->getValues();
            // var_dump($term);
            //   die();
            $term->setFromArray($values);
            $term->save();
            $db->commit();
            $form->addNotice('Your changes have been saved');
         } catch (Exception $e) {
            $db->rollBack();
            throw $e;
         }
      }
   }

}
