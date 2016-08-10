<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */

/**
 * Form create quick playlist when clicking on the Add To button under a video, to add that video to a new playlist.
 */

class Ynultimatevideo_Form_Playlist_QuickCreate extends Engine_Form {
    public function init() {        
        $this->setAttrib('id', 'ynultimatevideo_quick_create_playlist');
        $this->setAttrib('class', 'ynultimatevideo_quick_create_playlist global_form');
        
        $roles = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );
        
        $this->addElement('Text', 'title', 
            array(
                'required' => true,
                'allowEmpty' => false,
                'class' => 'ynultimatevideo_playlist_name',
                'decorators' => array('ViewHelper'),
                'rows' => 3,
                'label' => 'Playlist Name',
                'maxlength' => '63',
                'filters' => array(
					//new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
                    'StripTags',
                )
            )
        );
        
        // Element: auth_view
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynultimatevideo_playlist', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($roles, array_flip($viewOptions));
        $privacy = $this->createElement('radio','auth_view');
        $privacy->setDescription('Who may see this playlist?');
        $privacy->setLabel('Privacy');
        $privacy->addMultiOptions($viewOptions);
        $privacy->setValue('owner');
        
        $this->addElement($privacy);
        
        $this->addElement('Hidden', 'video_id');
        
        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Create Playlist',
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));
    }
}
?>