<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Video_Create extends Engine_Form
{
    protected $_parent_type;
    protected $_parent_id;
    protected $_roles;
    protected $_formArgs;

    public function setParent_type($value)
    {
        $this->_parent_type = $value;
    }

    public function setParent_id($value)
    {
        $this->_parent_id = $value;
    }

    public function getFormArgs()
    {
        return $this->_formArgs;
    }

    public function setFormArgs($formArgs)
    {
        $this->_formArgs = $formArgs;
    }

    public function init()
    {
        // Init form
        $this->setAttrib('id', 'form-upload')->setAttrib('name', 'ynvideochannel_video_create')->setAttrib('enctype', 'multipart/form-data')->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $user = Engine_Api::_() -> user() -> getViewer();
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
        ));

        // Init url
        $this->addElement('Text', 'url', array(
            'label' => 'Video URL',
            'description' => 'Paste the web address of the Youtube video here.',
        ));
        $this->url->getDecorator("Description")->setOption("placement", "append");

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Video Title',
            'maxlength' => '100',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '100')),
            )
        ));
        $this->title->setAttrib('required', true);

        // init tag
        $this->addElement('Text', 'tags', array(
            'label' => 'Tags / Keywords',
            'description' => 'Separate tags with commas.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->tags->getDecorator("Description")->setOption("placement", "append");

        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Video Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        // Add subforms
        if (!$this->_video) {
            $customFields = new Ynvideochannel_Form_Custom_Fields($this->_formArgs);
        } else {
            $customFields = new Ynvideochannel_Form_Custom_Fields(array_merge(array(
                'item' => $this->_video,
            ), $this->_formArgs));
        }
        if (get_class($this) == 'Ynvideochannel_Form_Video_Create') {
            $customFields->setIsCreation(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        if (!$this->_video) {
            $this -> addElement('Dummy', 'video_image', array(
                'label' => 'Original Image'
            ));
        } else {
            if (!empty($this->_video->photo_id)) {
                $photo = new Engine_Form_Element_Image('current_photo',
                    array(
                        'label' => 'Current Photo',
                        'src' => $this->_video->getPhotoUrl(),
                        'class' => 'ynvideochannel_sharevideo_image',
                        'onclick' => 'return false;',
                        'style' => 'width:200px;min-width:200px;padding:0;'
                    ));
                $this->addElement($photo);
            }
        }

        $this->addElement('File', 'photo', array(
            'label' => 'Image',
            'description' => 'Recommended aspect ratio 16:9.',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $this->photo->getDecorator('Description')->setOption('placement', 'append');

        // Init search
        $this->addElement('Checkbox', 'search', array(
            'label' => "Show this video in search results",
            'value' => 1,
        ));

        // View

        if ($this -> _parent_type == 'group')
        {
            $this -> _roles = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'parent_member' => 'Group Members',
                'owner' => 'Just Me',
            );
        }
        else
        {
            $this -> _roles = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me',
            );
        }

        $viewOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynvideochannel_video', $user, 'auth_view');
        $viewOptions = array_intersect_key($this -> _roles, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) >= 1)
        {
            // Make a hidden field
            if (count($viewOptions) == 1)
            {
                $this -> addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
                // Make select box
            }
            else
            {
                $this -> addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this video?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this -> auth_view -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }

        // Comment
        $commentOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynvideochannel_video', $user, 'auth_comment');
        $commentOptions = array_intersect_key($this -> _roles, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1)
        {
            // Make a hidden field
            if (count($commentOptions) == 1)
            {
                $this -> addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
                // Make select box
            }
            else
            {
                $this -> addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this video?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this -> auth_comment -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }

        $this->addElement('Hidden', 'code', array('order' => 100));
        $this->addElement('Hidden', 'duration', array('value' => 0, 'order' => 101));
        $this->addElement('Hidden', 'largeThumbnail', array('order' => 102));
        // Init submit
        $this->addElement('Button', 'upload', array(
            'label' => 'Share Video',
            'type' => 'submit',
        ));
    }

}
