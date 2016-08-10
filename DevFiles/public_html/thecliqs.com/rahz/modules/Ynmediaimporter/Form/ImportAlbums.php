<?php

class Ynmediaimporter_Form_ImportAlbums extends Engine_Form
{
    public function init()
    {
        $user_level = Engine_Api::_() -> user() -> getViewer() -> level_id;
        $user = Engine_Api::_() -> user() -> getViewer();

        // Init form
        $this 
         -> setTitle('Social Media Importer - Import Media')
         -> setDescription('Import media from social network to enrich your content.')
         -> setAttrib('class', 'global_form_popup')
         -> setAttrib('id', 'form-check')
         -> setAttrib('name', 'albums_create')
         -> setAttrib('enctype', 'multipart/form-data') 
         -> setAction(Zend_Controller_Front::getInstance()
         -> getRouter() -> assemble(array('controller'=>'import','action'=>'albums','format'=>'smoothbox'),'ynmediaimporter_extended',1));
        
        //ADD AUTH STUFF HERE
        

        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );

        // Init search
        $this -> addElement('Checkbox', 'search', array(
            'label' => Zend_Registry::get('Zend_Translate') -> _("Show this album in search results"),
            'value' => 1,
            'disableTranslator' => true
        ));

        // Element: auth_view
        $viewOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('album', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

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
                    'description' => 'Who may see this album?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this -> auth_view -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }

        // Element: auth_comment
        $commentOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('album', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

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
                    'description' => 'Who may post comments on this album?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this -> auth_comment -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }

        // Element: auth_tag
        $tagOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('album', $user, 'auth_tag');
        $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));

        if (!empty($tagOptions) && count($tagOptions) >= 1)
        {
            // Make a hidden field
            if (count($tagOptions) == 1)
            {
                $this -> addElement('hidden', 'auth_tag', array('value' => key($tagOptions)));
                // Make select box
            }
            else
            {
                $this -> addElement('Select', 'auth_tag', array(
                    'label' => 'Tagging',
                    'description' => 'Who may tag photos in this album?',
                    'multiOptions' => $tagOptions,
                    'value' => key($tagOptions),
                ));
                $this -> auth_tag -> getDecorator('Description') -> setOption('placement', 'append');
            }
        }
        

        // Submit or succumb!
        $this -> addElement('Button', '_continue2', array(
            'label' => 'Continue',
            'type' => 'submit',
            'decorators' => array('ViewHelper')
        ));

        $this -> addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'href' => '',
                'onclick' => 'parent.Smoothbox.close();',
                'decorators' => array(
                'ViewHelper'
            )
        ));
        
        $this -> addElement('text', 'ynmediaimporter_json_data', array(
            'value' => '',
            'decorators'=>array('ViewHelper'),
        ));
        
        $this -> addDisplayGroup(array(
            '_continue2',
            'cancel'
        ), 'buttons');
    }

    public function clearAlbum()
    {
        $this -> getElement('album') -> setValue(0);
    }

    public function saveValues()
    {
        $set_cover = false;
        $values = $this -> getValues();
        $params = Array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id'])))
        {
            $params['owner_id'] = Engine_Api::_() -> user() -> getViewer() -> user_id;
            $params['owner_type'] = 'user';
        }
        else
        {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
            throw new Zend_Exception("Non-user album owners not yet implemented");
        }

        if (($values['album'] == 0))
        {
            $params['title'] = $values['title'];
            if (empty($params['title']))
            {
                $params['title'] = "Untitled Album";
            }
            $params['category_id'] = (int)@$values['category_id'];
            $params['description'] = $values['description'];
            $params['search'] = $values['search'];

            $album = Engine_Api::_() -> getDbtable('albums', 'album') -> createRow();
            $album -> setFromArray($params);
            $album -> save();

            $set_cover = true;

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_() -> authorization() -> context;
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'registered',
                'everyone'
            );

            if (empty($values['auth_view']))
            {
                $values['auth_view'] = key($form -> auth_view -> options);
                if (empty($values['auth_view']))
                {
                    $values['auth_view'] = 'everyone';
                }
            }
            if (empty($values['auth_comment']))
            {
                $values['auth_comment'] = key($form -> auth_comment -> options);
                if (empty($values['auth_comment']))
                {
                    $values['auth_comment'] = 'owner_member';
                }
            }
            if (empty($values['auth_tag']))
            {
                $values['auth_tag'] = key($form -> auth_tag -> options);
                if (empty($values['auth_tag']))
                {
                    $values['auth_tag'] = 'owner_member';
                }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $tagMax = array_search($values['auth_tag'], $roles);

            foreach ($roles as $i => $role)
            {
                $auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
                $auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
                $auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
            }
        }
        else
        {
            if (!isset($album))
            {
                $album = Engine_Api::_() -> getItem('album', $values['album']);
            }
        }
        return $album;
    }

}
