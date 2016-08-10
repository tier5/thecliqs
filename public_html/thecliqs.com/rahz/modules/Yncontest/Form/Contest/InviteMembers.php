<?php
class Yncontest_Form_Contest_InviteMembers extends Engine_Form
{
  public function init()
  {
   
  	$this
  	->setTitle('Invite Members')
  	->setDescription('Choose the people you want to invite to this contest.')
  	->setAttrib('id', 'group_form_friends_invite')
  	->setAttrib('action','javascript:;')
  	;
  	
  	$this->addElement('Text','friends_search',array(
  			'description' =>'(Filter the search box and press Enter to search)',
  			'onkeypress' => 'updateUserList(event, "friends")',
  	));
  	$this->friends_search->getDecorator("Description")->setOption("placement", "append");
  	
  	$this->addElement('Checkbox', 'all', array(
  			'id' => 'friendselectall',
  			'label' => 'Choose All',
  			'ignore' => true
  	));
  	
  	$this->addElement('MultiCheckbox', 'friends', array(
  			'label' => 'Members',
  	));
  	
  	$this->addElement('Button', 'button', array(
  			'label' => 'Send Invites',
  			'onclick'=>'submitForm("friends")',
  			'ignore' => true,
  			'decorators' => array(
  					'ViewHelper',
  			),
  	));
  	
  	$this->addElement('Cancel', 'cancel', array(
  			'label' => 'cancel',
  			'link' => true,
  			'prependText' => ' or ',
  			'onclick' => 'parent.Smoothbox.close();',
  			'decorators' => array(
  					'ViewHelper',
  			),
  	));
  	
  	$this->addDisplayGroup(array('button', 'cancel'), 'buttons');
  }
}

