<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Form_Playlist_Create extends Engine_Form {
    protected $_roles;

    public function init() {
        // Init form
        $this->setAttrib('name', 'ynultimatevideo_playlist_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Playlist Name',
            'maxlength' => '63',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
//                new Engine_Filter_HtmlSpecialChars(),
				'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));
        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Playlist Description',
            'maxlength' => '300',
            'filters' => array(
                'StripTags',
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '300')),
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
                    'view_mode' => 0
                )
            )),
        ));

        $this->_roles = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );

        // Element: auth_view
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynultimatevideo_playlist', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($this->_roles, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                        'label' => 'Privacy',
                    'description' => 'Who may see this playlist?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Element: auth_comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynultimatevideo_playlist', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($this->_roles, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this playlist?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        $this->createImageFileElement();

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'onclick' => 'removeSubmit()',
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

    protected function initValueForElements() {
    }
    
    protected function createImageFileElement() {
        // Init playlist image
        $this->addElement('File', 'photo', array(
            'label' => 'Playlist Image',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    }
}