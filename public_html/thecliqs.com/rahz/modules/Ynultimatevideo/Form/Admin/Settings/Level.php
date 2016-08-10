<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function getSettingsValues() {
        $values = $this->getValues();
        $videoValues = array();
        $playlistValues = array();
        foreach($values as $key => $val) {
            $data = explode('_', $key, 2);
            if ($data[0] == 'video') {
                $videoValues[$data[1]] = $val;
            } else if ($data[0] == 'playlist') {
                $playlistValues[$data[1]] = $val;
            }
        }
        return array('video' => $videoValues, 'playlist' => $playlistValues);
    }
    
    public function init() {
        parent::init();

        // My stuff
        $this->setTitle('Member Level Settings')
                ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

        // Element: view
        $this->addElement('Radio', 'video_view', array(
            'label' => 'Allow Viewing of Videos?',
            'description' => 'Do you want to let members view videos? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow viewing of all videos, even private ones.',
                1 => 'Yes, allow viewing of videos.',
                0 => 'No, do not allow videos to be viewed.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
            unset($this->video_view->options[2]);
        }

        if (!$this->isPublic()) {

            // Element: create
            $this->addElement('Radio', 'video_create', array(
                'label' => 'Allow Creation of Videos?',
                'description' => 'Do you want to let members create videos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view videos, but only want certain levels to be able to create videos.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of videos.',
                    0 => 'No, do not allow video to be created.'
                ),
                'value' => 1,
            ));

            // Element: edit
            $this->addElement('Radio', 'video_edit', array(
                'label' => 'Allow Editing of Videos?',
                'description' => 'Do you want to let members edit videos? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all videos.',
                    1 => 'Yes, allow members to edit their own videos.',
                    0 => 'No, do not allow members to edit their videos.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->video_edit->options[2]);
            }

            // Element: delete
            $this->addElement('Radio', 'video_delete', array(
                'label' => 'Allow Deletion of Videos?',
                'description' => 'Do you want to let members delete videos? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all videos.',
                    1 => 'Yes, allow members to delete their own videos.',
                    0 => 'No, do not allow members to delete their videos.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->video_delete->options[2]);
            }

            // Element: comment
            $this->addElement('Radio', 'video_comment', array(
                'label' => 'Allow Commenting on Videos?',
                'description' => 'Do you want to let members of this level comment on videos?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all videos, including private ones.',
                    1 => 'Yes, allow members to comment on videos.',
                    0 => 'No, do not allow members to comment on videos.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->video_comment->options[2]);
            }

            // Element: upload
            $this->addElement('Radio', 'video_upload', array(
                'label' => 'Allow Video Upload?',
                'description' => 'Do you want to let members to upload their own videos? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    1 => 'Yes, allow video uploads.',
                    0 => 'No, do not allow video uploads.',
                ),
                'value' => 1,
            ));

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'video_auth_view', array(
                'label' => 'Video Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their video. If you do not check any options, everyone will be allowed to view videos.',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks (user videos only)',
                    'owner_member_member' => 'Friends of Friends (user videos only)',
                    'owner_member' => 'Friends Only (user videos only)',
                    'parent_member' => 'Group Members (group videos only)',
                    'owner' => 'Just Me',
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'parent_member','owner'),
            ));

            // Element: auth_comment
            $this->addElement('MultiCheckbox', 'video_auth_comment', array(
                'label' => 'Video Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their video. If you do not check any options, everyone will be allowed to post comments on media.',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks (user videos only)',
                    'owner_member_member' => 'Friends of Friends (user videos only)',
                    'owner_member' => 'Friends Only (user videos only)',
                    'parent_member' => 'Group Members (group videos only)',
                    'owner' => 'Just Me',
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'parent_member','owner'),
            ));

            // Element: max
            $this->addElement('Text', 'video_max', array(
                'label' => 'Maximum Allowed Videos',
                'description' => 'Enter the maximum number of allowed videos. The field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));
            
            // Playlist privacy

            // Element: view
            $this->addElement('Radio', "playlist_view", array(
                'label' => 'Allow Viewing of Video Playlists?',
                'description' => 'Do you want to let members view video playlists? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow viewing of all video playlists, even private ones.',
                    1 => 'Yes, allow viewing of video playlists.',
                    0 => 'No, do not allow video playlists to be viewed.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->playlist_view->options[2]);
            }

            if (!$this->isPublic()) {

                // Element: create
                $this->addElement('Radio', 'playlist_create', array(
                    'label' => 'Allow Creation of Video Playlists?',
                    'description' => 'Do you want to let members create video playlists? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view videos, but only want certain levels to be able to create video playlists.',
                    'multiOptions' => array(
                        1 => 'Yes, allow creation of video playlists.',
                        0 => 'No, do not allow video playlist to be created.'
                    ),
                    'value' => 1,
                ));

                // Element: edit
                $this->addElement('Radio', 'playlist_edit', array(
                    'label' => 'Allow Editing of Video Playlists?',
                    'description' => 'Do you want to let members edit video playlists? If set to no, some other settings on this page may not apply.',
                    'multiOptions' => array(
                        2 => 'Yes, allow members to edit all video playlists.',
                        1 => 'Yes, allow members to edit their own video playlists.',
                        0 => 'No, do not allow members to edit their video playlists.',
                    ),
                    'value' => ( $this->isModerator() ? 2 : 1 ),
                ));
                if (!$this->isModerator()) {
                    unset($this->playlist_edit->options[2]);
                }

                // Element: delete
                $this->addElement('Radio', 'playlist_delete', array(
                    'label' => 'Allow Deletion of Video Playlists?',
                    'description' => 'Do you want to let members delete video playlists? If set to no, some other settings on this page may not apply.',
                    'multiOptions' => array(
                        2 => 'Yes, allow members to delete all video playlists.',
                        1 => 'Yes, allow members to delete their own video playlists.',
                        0 => 'No, do not allow members to delete their video playlists.',
                    ),
                    'value' => ( $this->isModerator() ? 2 : 1 ),
                ));
                if (!$this->isModerator()) {
                    unset($this->playlist_delete->options[2]);
                }

                // Element: comment
                $this->addElement('Radio', 'playlist_comment', array(
                    'label' => 'Allow Commenting on Video Playlists?',
                    'description' => 'Do you want to let members of this level comment on video playlists?',
                    'multiOptions' => array(
                        2 => 'Yes, allow members to comment on all video playlists, including private ones.',
                        1 => 'Yes, allow members to comment on video playlists.',
                        0 => 'No, do not allow members to comment on video playlists.',
                    ),
                    'value' => ( $this->isModerator() ? 2 : 1 ),
                ));
                if (!$this->isModerator()) {
                    unset($this->playlist_comment->options[2]);
                }
            }
            
            // Element: auth_view
            $this->addElement('MultiCheckbox', 'playlist_auth_view', array(
                'label' => 'Video Playlist Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their video. If you do not check any options, everyone will be allowed to view video playlists.',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me',
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            // Element: auth_comment
            $this->addElement('MultiCheckbox', 'playlist_auth_comment', array(
                'label' => 'Video Playlist Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their video. If you do not check any options, everyone will be allowed to post comments on the playlist.',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me',
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));
        }
    }
}