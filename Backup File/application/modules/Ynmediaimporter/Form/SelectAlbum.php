<?php

class Ynmediaimporter_Form_SelectAlbum extends Engine_Form
{
    public function init()
    {
        $user_level = Engine_Api::_() -> user() -> getViewer() -> level_id;
        $user = Engine_Api::_() -> user() -> getViewer();

        // Init form
        $this -> setTitle('Social Media Importer - Choose Album') -> setDescription('Choose album or create new album to import media.') -> setAttrib('id', 'form-upload') -> setAttrib('name', 'albums_create') -> setAttrib('enctype', 'multipart/form-data') -> setAttrib('class', 'global_form_popup') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));

        // Init album
        $album_type= Engine_Api::_()->hasItemType('advalbum_album')?'advalbum_album':'album';
        $albumModule =  $album_type== 'advalbum_album'?'advalbum':'album';
        
        $albumTable = Engine_Api::_() -> getItemTable($album_type);
        
        
        $myAlbums = $albumTable -> select() -> from($albumTable, array(
            'album_id',
            'title'
        )) -> where('owner_type = ?', 'user') -> where('owner_id = ?', Engine_Api::_() -> user() -> getViewer() -> getIdentity()) -> query() -> fetchAll();

        $albumOptions = array('0' => 'Create A New Album');
        foreach ($myAlbums as $myAlbum)
        {
            $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
        }

        $this -> addElement('Select', 'album', array(
            'label' => 'Choose Album',
            'multiOptions' => $albumOptions,
            'onchange' => "updateTextFields()",
        ));

        // Init name
        $this -> addElement('Text', 'title', array(
            'label' => 'Album Title',
            'maxlength' => '40',
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength( array('max' => '63')),
            )
        ));

        
        
        // prepare categories
        $categories = $this -> getCategoriesAssoc();
        if (count($categories) > 0)
        {
            $this -> addElement('Select', 'category_id', array(
                'label' => 'Category',
                'multiOptions' => $categories,
            ));
        }

        // Init descriptions
        $this -> addElement('Textarea', 'description', array(
            'label' => 'Album Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

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
            'decorators' => array('ViewHelper')
        ));

        $this -> addElement('text', 'ynmediaimporter_json_data', array(
            'value' => '',
            'decorators' => array('ViewHelper'),
        ));

        /**
         * setup secheduler & start to process with information.
         */        
        $this -> addDisplayGroup(array(
            '_continue2',
            'cancel'
        ), 'buttons');
    }

    public function getCategoriesAssoc()
    {
      $albumModule= Engine_Api::_()->hasItemType('advalbum_album')?'advalbum':'album';
      
      $table = Engine_Api::_() -> getDbtable('categories', $albumModule);
      
      $data = array();
        
      $stmt = $table->select()
            ->from($table, array('category_id', 'category_name'))
            ->order('category_name ASC')
            ->query()
            ;
        foreach( $stmt->fetchAll() as $category ) {
          $data[$category['category_id']] = $category['category_name'];
        }
        return $data;
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
        $album_type= Engine_Api::_()->hasItemType('advalbum_album')?'advalbum_album':'album';
        
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

            $album = Engine_Api::_() -> getItemTable($album_type) -> createRow();
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
                $album = Engine_Api::_() -> getItem($album_type, $values['album']);
            }
        }
        return $album;
    }
}
