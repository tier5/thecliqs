<?php
class Mp3music_Form_EditPlaylist extends Mp3music_Form_CreatePlaylist
{
  public function init()
  {
    // Init form
    parent::init();
    $this
      ->setDescription('')
      ->setAttrib('id',      'form-upload-music')
      ->setAttrib('name',    'playlist_edit')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    // Pre-fill form values
    $this->addElement('Hidden', 'playlist_id');    
    // Override submit button
    $this->removeElement('submit');
    $this->addElement('Button', 'save', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }

  public function populate($playlist)
  {
    $this->setTitle('Edit Playlist');
    foreach (array(
      'playlist_id' => $playlist->getIdentity(),
      'title'       => htmlspecialchars_decode($playlist->getTitle()),
      'description' => htmlspecialchars_decode($playlist->description),
      'search'      => $playlist->search,
      ) as $key => $value) {
        $this->getElement($key)->setValue($value);
    }
    // AUTHORIZATIONS
    $auth = Engine_Api::_()->authorization()->context;
    $lowest_viewer = array_pop(array_keys($this->_roles));
    foreach (array_reverse(array_keys($this->_roles)) as $role) {
      if ($auth->isAllowed($playlist, $role, 'view')) {
        $lowest_viewer = $role;
      }
    }
     if($this->getElement('auth_view'))
    {
    $this->getElement('auth_view')->setValue($lowest_viewer);
    }
    $lowest_commenter = array_pop(array_keys($this->_roles));
    foreach (array_reverse(array_keys($this->_roles)) as $role) {
      if ($auth->isAllowed($playlist, $role, 'comment')) {
        $lowest_commenter = $role;
      }
    }
     if($this->getElement('auth_comment'))
    {
    $this->getElement('auth_comment')->setValue($lowest_commenter);
    }
  }
  public function saveValues()
  {
       $translate= Zend_Registry::get('Zend_Translate');      
    $playlist = parent::saveValues();
    $values   = $this->getValues();
    if ($playlist && $playlist->isEditable()) {
      $playlist->title  = trim(htmlspecialchars($values['title']));
      if(trim($playlist->title) == '')
      {
            $playlist->title = $translate->_('_MUSIC_UNTITLED_PLAYLIST');
      }
       $str = $playlist->title;
      $str= strtolower($str);
      $str= preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/","a",$str);  
      $str= preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/","e",$str);  
      $str= preg_replace("/(ì|í|ị|ỉ|ĩ)/","i",$str);  
      $str= preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/","o",$str);  
      $str= preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/","u",$str);  
      $str= preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/","y",$str);  
      $str= preg_replace("/(đ)/","d",$str);  
      $str= preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/","-",$str); 
      $str= preg_replace("/(-+-)/","-",$str); //thay thế 2- thành 1- 
      $str= preg_replace("/(^\-+|\-+$)/","",$str); 
      $str= preg_replace("/(-)/"," ",$str); 
      $playlist->title_url = trim(htmlspecialchars($str));
      $playlist->description = trim(htmlspecialchars($values['description']));
      $playlist->search      = $values['search'];
      $playlist->save();
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($playlist) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
      
      return $playlist;
    } else {
      return false;
    }
  }
}
