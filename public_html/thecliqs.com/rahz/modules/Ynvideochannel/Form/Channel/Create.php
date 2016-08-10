<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Channel_Create extends Engine_Form
{
    protected $_cover;
    protected $_thumb;
    protected $_url;
    protected $_videos;
    protected $_parent_type;
    protected $_parent_id;
    protected $_formArgs;
    protected $_roles;
    protected $_channel;

    public function setParent_type($value)
    {
        $this->_parent_type = $value;
    }

    public function setParent_id($value)
    {
        $this->_parent_id = $value;
    }

    public function setChannel($value)
    {
        $this->_channel = $value;
    }

    public function getFormArgs()
    {
        return $this->_formArgs;
    }
    public function setFormArgs($formArgs)
    {
        $this->_formArgs = $formArgs;
    }
    public function setCover($value)
    {
        $this->_cover = $value;
    }
    public function setThumb($value)
    {
        $this->_thumb = $value;
    }
    public function setUrl($value)
    {
        $this->_url = $value;
    }
    public function setVideos($value)
    {
        $this->_videos = $value;
    }
    public function init()
    {
        $user = Engine_Api::_() -> user() -> getViewer();
        $view = Zend_Registry::get('Zend_View');
        // Init form
        $this->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'ynvideochannel_channel_create')
            ->setAttrib('enctype', 'multipart/form-data')->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this -> addElement('Dummy', 'add_channel', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_addChannnel.tpl',
                    'url' => $this -> _url,
                    'class' => 'form element',
                )
            )),
        ));

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Channel Title',
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

        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
        ));

        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Channel Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        $coverLabel = $this->_channel ? $view->translate("Current Cover") : $view->translate("Original Cover");
        $thumbnailLabel = $this->_channel ? $view->translate("Current Thumbnail") : $view->translate("Original Thumbnail");

        if($this ->_cover)

            $this -> addElement('Dummy', 'channel_image', array(
                'decorators' => array( array(
                    'ViewScript',
                    array(
                        'viewScript' => '_channelCover.tpl',
                        'cover' => $this ->_cover,
                        'class' => 'form element',
                        'label' => $coverLabel,
                    )
                )),
            ));

        $this->addElement('File', 'cover_photo', array(
            'label' => 'Cover Image',
        ));
        $this->cover_photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $this->cover_photo->getDecorator('Description')->setOption('placement', 'append');

        if($this ->_thumb)
            $this -> addElement('Dummy', 'channel_image_1', array(
                'decorators' => array( array(
                    'ViewScript',
                    array(
                        'viewScript' => '_channelThumb.tpl',
                        'thumb' => $this ->_thumb,
                        'class' => 'form element',
                        'label' => $thumbnailLabel
                    )
                )),
            ));

        $this->addElement('File', 'thumbnail', array(
            'label' => 'Thumbnail Image',
        ));
        $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $this->thumbnail->getDecorator('Description')->setOption('placement', 'append');

        // Init search
        $this->addElement('Checkbox', 'search', array(
            'label' => "Show this channel in search results",
            'value' => 1,
        ));

        $this -> _roles = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me',
        );

        $viewOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynvideochannel_channel', $user, 'auth_view');
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
                    'description' => 'Who may see this channel?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this -> auth_view -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }

        // Comment
        $commentOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynvideochannel_channel', $user, 'auth_comment');
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
                    'description' => 'Who may post comments on this channel?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this -> auth_comment -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }

        $this -> addElement('Dummy', 'videos', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_channelVideos.tpl',
                    'videos' => $this ->_videos,
                    'itemPerPage' => 10,
                    'class' => 'form element',
                )
            )),
        ));

        // Init submit
        $this->addElement('Button', 'upload', array(
            'label' => 'Save Channel',
            'type' => 'submit',
            'order' => 100
        ));
    }

}
