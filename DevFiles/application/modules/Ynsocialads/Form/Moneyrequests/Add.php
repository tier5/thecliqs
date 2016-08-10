<?php
class Ynsocialads_Form_Moneyrequests_Add extends Engine_Form {

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $min_amount = number_format(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynsocialads_money', 'min_amount'),2);
        if ($min_amount == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynsocialads_money')
            ->where('name = ?', 'min_amount'));
            if ($row) {
                $min_amount = $row->value;
            }
        }
        $max_amount = number_format(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynsocialads_money', 'max_amount'),2);
        if ($max_amount == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynsocialads_money')
            ->where('name = ?', 'max_amount'));
            if ($row) {
                $max_amount = $row->value;
            }
        }
        
        $virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
        $select = $virtualTable -> select() -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1);
        $virtual_money = $virtualTable -> fetchRow($select);
        $remaining = $virtual_money -> remain;
        $total = $virtual_money -> total;
        
        $max = ($max_amount < $remaining) ? $max_amount : $remaining;
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'method'=>'POST',
        ));
        
        $this
          ->setTitle('Request Money')
          ->setDescription('Request amount must be between $'.$min_amount.' and $'.$max);
          
        $this->addElement('Float', 'amount', array(
            'label' => 'Request Amount',
            'required' => true,
            'validators' => array(
                new Zend_Validate_Between(array('min' => $min_amount, 'max' => $max))
            ),
        ));
        
        $this->addElement('Text', 'paypal_email', array(
            'label' => 'Paypal Email',
            'required' => true,
        ));
        
        $this->addElement('Textarea', 'request_message', array(
            'label' => 'Request Message',
        ));
        
        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Add',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
            'onclick' => 'javascript:parent.Smoothbox.close()',
        ));
        
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array());
    }
}