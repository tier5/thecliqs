<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Ynauction_Form_Proposal
 * @author     Minh Nguyen
 */
class Ynauction_Form_Proposal extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Proposal Price')
      ->setAttrib('id',      'form-proposal-price')
      ->setAttrib('name',    'proposal_price')
      ->setAttrib('class',   '')   
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'ynauction_proposal'))  
      ;
    $viewer = Engine_Api::_()->user()->getViewer();
    // Init conact name
    $this->addElement('Text', 'proposal_price', array(
      'label' => 'Your Price',
      'maxlength' => '63',
      'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Proposal',
      'type'  => 'submit',
      'decorators' => array('ViewHelper') 
    ));
	 $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      )));
  }

  public function saveValues($product)
  {
      $values   = $this->getValues(); 
	  $viewer = Engine_Api::_()->user()->getViewer();
      if(!is_numeric($values['proposal_price']) || $values['proposal_price'] <= 0)
      {
          $this->getElement('proposal_price')->addError('The proposal price number is invalid! (Ex: 2000.25)');
          return false;
      }
      if($product)
      {
           $proposal_price =  $values['proposal_price'];
           if(Engine_Api::_()->ynauction()->checkBoughtPrice($product->product_id, $proposal_price))
           {
                 $this->getElement('proposal_price')->addError('The proposal price existed, please enter other price!');
                 return false;
           }
           $table = Engine_Api::_()->getItemTable('ynauction_proposal');
           $proposal = $table->createRow();
           $proposal->product_id = $product->product_id;
           $proposal->ynauction_user_id = $viewer->user_id;
           $proposal->proposal_price = $proposal_price;
           $proposal->proposal_time = date('Y-m-d H:i:s');
           $proposal->save();
           return true;
      }
      else
        return false; 
  }
}
