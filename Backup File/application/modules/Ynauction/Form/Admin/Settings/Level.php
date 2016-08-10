<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     YnAuction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Level.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("YNAUCTION_FORM_ADMIN_LEVEL_DESCRIPTION");

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of auctions?',
      'description' => 'Do you want to let members view auctions? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all auctions, even private ones.',
        1 => 'Yes, allow viewing of auctions.',
        0 => 'No, do not allow auctions to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of auctions?',
        'description' => 'YNAUCTION_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes, allow creation of auctions.',
          0 => 'No, do not allow auctions to be created.'
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of auctions?',
        'description' => 'Do you want to let members edit auctions? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all auctions.',
          1 => 'Yes, allow members to edit their own auctions.',
          0 => 'No, do not allow members to edit their auctions.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of auctions?',
        'description' => 'Do you want to let members delete auctions? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all auctions.',
          1 => 'Yes, allow members to delete their own auctions.',
          0 => 'No, do not allow members to delete their auctions.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on auctions?',
        'description' => 'Do you want to let members of this level comment on auctions?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all auctions, including private ones.',
          1 => 'Yes, allow members to comment on auctions.',
          0 => 'No, do not allow members to comment on auctions.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      $this->addElement('Radio', 'approve_seller', array(
      'label' => 'Allow auto approve become seller?',
      'description' => '',
      'multiOptions' => array(
          1 => 'Yes, allow auto approve become seller.',
          0 => 'No, do not  allow auto approve become seller.'
        ),
        'value' => 0,
      ));
      
      $this->addElement('Radio', 'auto_approve', array(
      'label' => 'Allow auto approve auction?',
      'description' => '',
      'multiOptions' => array(
          1 => 'Yes, allow auto approve auction.',
          0 => 'No, do not allow auto approve auction.'
        ),
        'value' => 0,
      ));
      
      $this->addElement('Text', 'publish_fee', array(
      'label' => 'Publish fee - '.Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD'),
      'description' => '',
      'value' => 0,
      ));
      
      $this->addElement('Text', 'feature_fee', array(
      'label' => 'Feature fee - '.Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD'),
      'description' => '',
      'value' => 0,
      ));
      
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Auctions Listing Privacy',
        'description' => 'YNAUCTION_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Auctions Comment Options',
        'description' => 'YNAUCTION_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'description' => '',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
      ));
    }
  }
}