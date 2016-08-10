<?php
class Mp3music_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();
    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('MUSIC_FORM_ADMIN_LEVEL_DESCRIPTION');
      $settings = Engine_Api::_()->getApi('settings', 'core');
    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Albums?',
      'description' => 'MUSIC_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all albums, even private ones.',
        1 => 'Yes, allow viewing of albums.',
        0 => 'No, do not allow albums to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
     if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
         
      $this->addElement('Radio', 'is_download', array(
      'label' => 'Allow Downloading Music',
      'description' => 'Allow users to download songs?',
      'multiOptions' => array(
        1 => 'Yes, allow users to download uploaded songs.',
        0 => 'No, do not allow users to download uploaded songs.',
      ),
      'value' => ($this->isPublic()) ? 0 : 1,
    ));
  
      if( !$this->isPublic() ) { 
      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Uploading Music?',
        'description' => 'Do you want to allow users to upload music to their profile?',
        'multiOptions' => array(
          1 => 'Yes, allow this member level to create albums',
          0 => 'No, do not allow this member level to create albums',
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Albums?',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_EDIT_DESCRIPTION',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all albums.',
          1 => 'Yes, allow members to edit their own albums.',
          0 => 'No, do not allow members to edit their albums.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Albums?',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_DELETE_DESCRIPTION',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all albums.',
          1 => 'Yes, allow members to delete their own albums.',
          0 => 'No, do not allow members to delete their albums.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Albums?',
        'description' => 'Do you want to let members of this level comment on albums?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all albums, including private ones.',
          1 => 'Yes, allow members to comment on albums.',
          0 => 'No, do not allow members to comment on albums.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Album Privacy',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Album Comment Options',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
   
        // Element:  max_songs
        $this->addElement('Text', 'max_songs', array(
          'label' => 'Maximum Songs',
          'description' => 'MUSIC_FORM_ADMIN_LEVEL_MAXSONGS_DESCRIPTION',
           'maxlength' => '3',
          'onKeyPress'=> "return checkIt(event)",
          'value' => $settings->getSetting("mp3music.maxSongsDefault", 30),
        ));
        // Element: max_filesize
        $this->addElement('Text', 'max_filesize', array(
          'label' => 'Maximum Filesize',
          'description' => 'MUSIC_FORM_ADMIN_LEVEL_MAXFILESIZE_DESCRIPTION',
          'maxlength' => '6',
          'onKeyPress'=> "return checkIt(event)",
          'value' => $settings->getSetting("mp3music.maxFilesizeDefault", 10000),
        ));
        // Element: max_storage
        $this->addElement('Text', 'max_storage', array(
          'label' => 'Maximum Storage',
          'description' => 'Maximum file storage for each user (KB)',
          'onKeyPress'=> "return checkIt(event)",
          'value' => $settings->getSetting("mp3music.maxStorageDefault", 100000),
        ));
       
        // Element: max_albums
         $this->addElement('Text', 'max_albums', array(
          'label' => 'Create New Album',
          'description' => 'Maximum numbers of created album? (Enter a number between 1 and 999)',
          'maxlength' => '3',
          'onKeyPress'=> "return checkIt(event)",
          'value' => $settings->getSetting("mp3music.maxAlbumsDefault", 10),
        ));
    }
  } 
}