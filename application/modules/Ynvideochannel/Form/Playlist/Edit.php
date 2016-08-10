<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Playlist_Edit extends Engine_Form {
    private $_playlist;
    private $_roles;

    public function getPlaylist() {
        return $this->_playlist;
    }

    public function setPlaylist($playlist) {
        return $this->_playlist = $playlist;
    }

    public function __construct($options = null) {
        if (is_array($options) && array_key_exists('playlist', $options)) {
            $this->_playlist = $options['playlist'];
            unset($options['playlist']);
        }

        parent::__construct($options);
    }

    protected function initValueForElements() {

        // fill in data for the authentication view and authentication comment element
        $authViewElement = $this->getElement('auth_view');
        $authCommentElement = $this->getElement('auth_comment');

        $auth = Engine_Api::_()->authorization()->context;
        if ($authViewElement && !$authViewElement instanceof Engine_Form_Element_Hidden) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_playlist, $key, 'view')) {
                    $authViewElement->setValue($key);
                    break;
                }
            }
        }
        if ($authCommentElement && !$authCommentElement instanceof Engine_Form_Element_Hidden) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_playlist, $key, 'comment')) {
                    $authCommentElement->setValue($key);
                    break;
                }
            }
        }
    }

    public function init() {
        // Init form
        $this->setAttrib('name', 'ynvideochannel_playlist_edit')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $user = Engine_Api::_() -> user() -> getViewer();

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Playlist Name',
            'maxlength' => '63',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));
        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Playlist Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        $this -> addElement('Select', 'category_id', array(
            'label' => 'Category',
        ));

        $this -> addElement('dummy', 'view_mode', array(
            'label'     => 'Viewing Mode',
            'required'  => true,
            'allowEmpty'=> false,
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_playlist_preview.tpl',
                    'class' => 'form element',
                    'view_mode' => $this->getPlaylist()->view_mode
                )
            )),
        ));

        // Privacy
        $this->_roles = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );

        $availableLabels = array(
            'everyone'            => 'Everyone',
            'registered'          => 'All Registered Members',
            'owner_network'       => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member'        => 'Friends Only',
            'owner'               => 'Just Me'
        );


        // View
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if( empty($viewOptions) ) {
            $viewOptions = $availableLabels;
        }

        if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
            // Make a hidden field
            if(count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this video?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Comment
        $commentOptions =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        if( empty($commentOptions) ) {
            $commentOptions = $availableLabels;
        }

        if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
            // Make a hidden field
            if(count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this video?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Init playlist image
        $this->addElement('File', 'photo', array(
            'label' => 'Playlist Image',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->createImageFileElement();

        if ($this->getPlaylist()->getVideoCount()) {
            $this ->addElement('heading', 'edit_songs_header', array(
                'label' => '',
                'description' => 'Click and drag to reorder videos in playlist'
            ));

            $this -> addElement('Dummy', 'manage_songs', array('decorators' => array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => '_playlist_edit_video_listing.tpl',
                        'class' => 'form element',
                        'playlist' => $this -> getPlaylist(),
                        'noedit' => true
                    )
                )),
            ));

            $edit_songs[] = 'manage_songs';
            $this->addDisplayGroup($edit_songs, 'edit_songs', array(
            ));
            $this->edit_songs->removeDecorator('Fieldset');
        }

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));

        $this->initValueForElements();
    }

    protected function createImageFileElement() {
        if (!empty($this->_playlist->photo_id)) {
            $photo = new Engine_Form_Element_Image('photo_delete',
                array(
                    'label' => 'Current Photo',
                    'src' => $this->_playlist->getPhotoUrl('thumb.normal'),
                    'style' => 'width:300px;min-width:300px;padding:0;',
                    'onclick' => 'return false;'
                ));
            $photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
            $this->addElement($photo);
            $this->addDisplayGroup(array('photo_delete'), 'photo_delete_group');
        }

        // Photo
        $file_element = new Engine_Form_Element_File('photo', array(
            'label' => 'Playlist Image (optional)',
            'description' => 'When a new photo is uploaded, the old one will be deleted',
            'size' => '40'
        ));
        $file_element->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement($file_element);
        $this->addDisplayGroup(array('photo'), 'photo_group');

        if (!empty($this->_post->photo_id)) {
            $this->getDisplayGroup('photo_group')->getDecorator('HtmlTag')->setOption('style', 'display:none;');
        }
    }

}