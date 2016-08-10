<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{

    public function getSettingsValues()
    {
        $values = $this->getValues();
        $videoValues = array();
        $playlistValues = array();
        $channelValues = array();
        foreach ($values as $key => $val) {
            $data = explode('_', $key, 2);
            if ($data[0] == 'video') {
                $videoValues[$data[1]] = $val;
            } else if ($data[0] == 'playlist') {
                $playlistValues[$data[1]] = $val;
            } else if ($data[0] == 'channel') {
                $channelValues[$data[1]] = $val;
            }
        }
        return array('video' => $videoValues, 'playlist' => $playlistValues, 'channel' => $channelValues);
    }

    public function init()
    {
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
            'value' => ($this->isModerator() ? 2 : 1),
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
                'value' => ($this->isModerator() ? 2 : 1),
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
                'value' => ($this->isModerator() ? 2 : 1),
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
                'value' => ($this->isModerator() ? 2 : 1),
            ));
            if (!$this->isModerator()) {
                unset($this->video_comment->options[2]);
            }

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
                    'parent_member' => 'Parent Members (subject videos only)',
                    'owner' => 'Just Me',
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'parent_member', 'owner'),
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
                    'parent_member' => 'Parent Members (subject videos only)',
                    'owner' => 'Just Me',
                ),
                'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'parent_member', 'owner'),
            ));
            $this->addElement('Text', 'video_max', array(
                'label' => 'Maximum Allowed Videos',
                'description' => 'Enter the maximum number of allowed videos. The field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));

            // User Credits
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $this->addElement('Integer', 'video_first_amount', array(
                    'label' => 'Credit for sharing new video',
                    'description' => 'No of First Actions',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'video_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'video_credit', array(
                    'description' => 'Credit for next action',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'video_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'video_period', array(
                    'description' => 'Period (days)',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
            }
        }
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
            'value' => ($this->isModerator() ? 2 : 1),
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
                'value' => ($this->isModerator() ? 2 : 1),
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
                'value' => ($this->isModerator() ? 2 : 1),
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
                'value' => ($this->isModerator() ? 2 : 1),
            ));
            if (!$this->isModerator()) {
                unset($this->playlist_comment->options[2]);
            }

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'playlist_auth_view', array(
                'label' => 'Video Playlist Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their playlists. If you do not check any options, everyone will be allowed to view video playlists.',
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
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their playlists. If you do not check any options, everyone will be allowed to post comments on the playlists.',
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
            $this->addElement('Text', 'playlist_max', array(
                'label' => 'Maximum Allowed Playlists',
                'description' => 'Enter the maximum number of allowed playlists. The field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $this->addElement('Integer', 'playlist_first_amount', array(
                    'label' => 'Credit for Creating Playlists',
                    'description' => 'No of First Actions',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'playlist_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'playlist_credit', array(
                    'description' => 'Credit for next action',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'playlist_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'playlist_period', array(
                    'description' => 'Period (days)',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
            }
        }


        // Channel privacy

        // Element: view
        $this->addElement('Radio', 'channel_view', array(
            'label' => 'Allow Viewing of Video Channels?',
            'description' => 'Do you want to let members view video channels? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow viewing of all video channels, even private ones.',
                1 => 'Yes, allow viewing of video channels.',
                0 => 'No, do not allow video channels to be viewed.',
            ),
            'value' => ($this->isModerator() ? 2 : 1),
        ));
        if (!$this->isModerator()) {
            unset($this->channel_view->options[2]);
        }

        if (!$this->isPublic()) {

            // Element: create
            $this->addElement('Radio', 'channel_create', array(
                'label' => 'Allow Creation of Video Channels?',
                'description' => 'Do you want to let members create video channels? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view videos, but only want certain levels to be able to create video channels.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of video channels.',
                    0 => 'No, do not allow video channels to be created.'
                ),
                'value' => 1,
            ));

            // Element: edit
            $this->addElement('Radio', 'channel_edit', array(
                'label' => 'Allow Editing of Video Channels?',
                'description' => 'Do you want to let members edit video channels? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all video channels.',
                    1 => 'Yes, allow members to edit their own video channels.',
                    0 => 'No, do not allow members to edit their video channels.',
                ),
                'value' => ($this->isModerator() ? 2 : 1),
            ));
            if (!$this->isModerator()) {
                unset($this->channel_edit->options[2]);
            }

            // Element: delete
            $this->addElement('Radio', 'channel_delete', array(
                'label' => 'Allow Deletion of Video Channels?',
                'description' => 'Do you want to let members delete video channels? If set to no, some other settings on this page may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all video channels.',
                    1 => 'Yes, allow members to delete their own video channels.',
                    0 => 'No, do not allow members to delete their video channels.',
                ),
                'value' => ($this->isModerator() ? 2 : 1),
            ));
            if (!$this->isModerator()) {
                unset($this->playlist_delete->options[2]);
            }

            // Element: comment
            $this->addElement('Radio', 'channel_comment', array(
                'label' => 'Allow Commenting on Video Channels?',
                'description' => 'Do you want to let members of this level comment on video channels?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all video channels, including private ones.',
                    1 => 'Yes, allow members to comment on video channels.',
                    0 => 'No, do not allow members to comment on video channels.',
                ),
                'value' => ($this->isModerator() ? 2 : 1),
            ));
            if (!$this->isModerator()) {
                unset($this->channel_comment->options[2]);
            }

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'channel_auth_view', array(
                'label' => 'Video Channel Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their channels. If you do not check any options, everyone will be allowed to view video channels.',
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
            $this->addElement('MultiCheckbox', 'channel_auth_comment', array(
                'label' => 'Video Channel Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their channels. If you do not check any options, everyone will be allowed to post comments on the channels.',
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
            $this->addElement('Text', 'channel_max', array(
                'label' => 'Maximum Allowed Channels',
                'description' => 'Enter the maximum number of allowed channels. The field must contain an integer, use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $this->addElement('Integer', 'channel_first_amount', array(
                    'label' => 'Credit for Adding Channels',
                    'description' => 'No of First Actions',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'channel_first_credit', array(
                    'description' => 'Credit/Action',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));

                $this->addElement('Integer', 'channel_credit', array(
                    'description' => 'Credit for next action',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'channel_max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'channel_period', array(
                    'description' => 'Period (days)',
                    'required' => true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
            }
        }
    }

}