<?php
class Mp3music_Form_Admin_Settings_Levelplaylist extends Authorization_Form_Admin_Level_Abstract
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
      'label' => 'Allow Viewing of Playlists?',
      'description' => 'MUSIC_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION_PLAYLIST',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all playlists, even private ones.',
        1 => 'Yes, allow viewing of playlists.',
        0 => 'No, do not allow playlists to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
     if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
     if( !$this->isPublic() ) {          
      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Create Playlist?',
        'description' => 'Do you want to allow users to create playlist?',
        'multiOptions' => array(
          1 => 'Yes, allow this member level to create playlists',
          0 => 'No, do not allow this member level to create playlists',
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Playlists?',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_EDIT_DESCRIPTION_PLAYLIST',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all playlists.',
          1 => 'Yes, allow members to edit their own playlists.',
          0 => 'No, do not allow members to edit their playlists.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Playlist?',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_DELETE_DESCRIPTION_PLAYLIST',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all playlists.',
          1 => 'Yes, allow members to delete their own playlists.',
          0 => 'No, do not allow members to delete their playlists.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Playlists?',
        'description' => 'Do you want to let members of this level comment on playlists?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all playlists, including private ones.',
          1 => 'Yes, allow members to comment on playlists.',
          0 => 'No, do not allow members to comment on playlists.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Playlist Privacy',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION_PLAYLIST',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone','registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Playlist Comment Options',
        'description' => 'MUSIC_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION_PLAYLIST',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone','registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
   // Element:  max_songs
        $this->addElement('Text', 'max_songs', array(
          'label' => 'Maximum Songs',
          'description' => 'MUSIC_FORM_ADMIN_LEVEL_PMAXSONGS_DESCRIPTION',
           'maxlength' => '3',
          'onKeyPress'=> "return checkIt(event)",
          'value' => $settings->getSetting("mp3music.maxSongsDefault", 30),
        ));
         // Element: max_playlists
         $this->addElement('Text', 'max_playlists', array(
          'label' => 'Create New Playlist',
          'description' => 'Maximum numbers of created playlist? (Enter a number between 1 and 999)',
          'maxlength' => '3',
          'onKeyPress'=> "return checkIt(event)",
          'value' => $settings->getSetting("mp3music.maxPlaylistsDefault", 10),
        ));
    }
  } 
}