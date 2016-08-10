<?php

class Ynmediaimporter_ImportController extends Core_Controller_Action_Standard
{

    public function checkAction()
    {
        $form = $this -> view -> form = new Ynmediaimporter_Form_Check;
    }

    public function albumsAction()
    {
        $this -> view -> form = $form = new Ynmediaimporter_Form_ImportAlbums();
        
        $request = $this -> _request;

        if ($request -> isPost() && isset($_POST['_continue']))
        {
            $form -> getElement('ynmediaimporter_json_data') -> setValue($_POST['ynmediaimporter_json_data']);
            return;
        }
        if ($request -> isPost() && isset($_POST['_continue2'])){
            
            $values = $_POST;
            $params = array();
            
            // init params for later bind
            foreach(array('search','auth_view','auth_comment','auth_tag') as $key){
                if(isset($values[$key])){
                    $params[$key] =  $values[$key];    
                }
            }
            
            $data = json_decode($_POST['ynmediaimporter_json_data'], 1);
            
            $schedulerId = Ynmediaimporter::setupScheduler($data['photos'], $data['albums'], 0, $params);
    
            $this -> _helper -> redirector -> gotoRoute(array(
                'controller' => 'import',
                'action' => 'process',
                'format' => 'smoothbox',
                'scheduler_id' => $schedulerId,
            ), 'ynmediaimporter_extended', 1);
                
        }
        
    }

    public function processAction()
    {
        $this -> view -> scheduler_id = $this -> _getParam('scheduler_id', 0);
    }

    public function selectAlbumAction()
    {

        if (!$this -> _helper -> requireAuth() -> setAuthParams('album', null, 'create') -> isValid())
            return;

        // Get form
        $this -> view -> form = $form = new Ynmediaimporter_Form_SelectAlbum();

        if (isset($_POST['_continue']))
        {
            $form -> getElement('ynmediaimporter_json_data') -> setValue($_POST['ynmediaimporter_json_data']);
            return;
        }

        if (!$this -> getRequest() -> isPost())
        {
            if (null !== ($album_id = $this -> _getParam('album_id')))
            {
                $form -> populate(array('album' => $album_id));
            }
            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }

        $album_type= Engine_Api::_()->hasItemType('advalbum_album')?'advalbum_album':'album';
        $photo_type= Engine_Api::_()->hasItemType('advalbum_photo')?'advalbum_photo':'photo';
        $tableAlbum = Engine_Api::_() -> getItemTable($album_type);
        $tablePhoto = Engine_Api::_() -> getItemTable($photo_type);
        
        $db = $tableAlbum-> getAdapter();
        $db -> beginTransaction();

        try
        {
            $album = $form -> saveValues();

            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        $data = json_decode($_POST['ynmediaimporter_json_data'], 1);

        $schedulerId = Ynmediaimporter::setupScheduler($data['photos'], null, $album -> album_id, array());

        $this -> _helper -> redirector -> gotoRoute(array(
            'controller' => 'import',
            'action' => 'process',
            'format' => 'smoothbox',
            'scheduler_id' => $schedulerId,
        ), 'ynmediaimporter_extended', 1);
    }

}
