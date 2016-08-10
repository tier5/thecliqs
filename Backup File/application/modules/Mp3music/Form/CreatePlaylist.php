<?php
class Mp3music_Form_CreatePlaylist extends Engine_Form
{
  protected $_playlist;
  protected $_roles = array(
        'everyone'             => 'Everyone',
        'registered'           => 'All Registered Members',
        'owner_network'        => 'Friends and Networks',
        'owner_member_member'  => 'Friends of Friends',
        'owner_member'         => 'Friends Only',
        'owner'                => 'Just Me'
      );

  public function init()
  {
    // Init form
    $this
      ->setTitle('Add New Playlist')
      ->setAttrib('id',      'form-upload-music')
      ->setAttrib('name',    'playlist_create')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Playlist Name',
      'maxlength' => '63',
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
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
    // Init search checkbox
    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this playlist in search results",
      'value' => 1,
      'checked' => true,
    ));
    // AUTHORIZATIONS
    $user_level   = Engine_Api::_()->user()->getViewer()->level_id;
    $allowed_view = @Engine_Api::_()->getApi('core', 'authorization')->getPermission($user_level, 'mp3music_playlist', 'auth_view');
    if (!empty($allowed_view) && strlen($allowed_view) > 2) {
      $allowed_view = Zend_Json_Decoder::decode($allowed_view);
      $viewPerms    = array();
      foreach( $allowed_view as $allowed ){
        $viewPerms[$allowed] = $this->_roles[$allowed];
      }
      $this->addElement('Select', 'auth_view', array(
        'label'        => 'Privacy',
        'description'  => 'Who may see this playlist?',
        'multiOptions' => $viewPerms,
        'value'        => array_shift(array_keys($viewPerms)),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    $allowed_comment = @Engine_Api::_()->authorization()->getPermission($user_level, 'mp3music_playlist', 'auth_comment');
    if (!empty($allowed_comment) && strlen($allowed_comment) > 2){
      $allowed_comment = Zend_Json_Decoder::decode($allowed_comment);
      $commentPerms    = array();
      foreach( $allowed_comment as $allowed ){
        $commentPerms[$allowed] = $this->_roles[$allowed];
      }
      $this->addElement('Select', 'auth_comment', array(
        'label'        => 'Comment Privacy',
        'description'  => 'Who may post comments on this playlist?',
        'multiOptions' => $commentPerms,
        'value'        => array_shift(array_keys($commentPerms))
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    } 
    // Init file uploader
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
                ->addDecorator('viewScript', array(
                  'viewScript' => '_edit_playlist.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload); 
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Playlist',
      'type'  => 'submit',
    ));                  
  }
  public function saveValues()
  {
    $playlist = null;
    $values   = $this->getValues();
    $translate= Zend_Registry::get('Zend_Translate');
    
    if(!empty($values['playlist_id']))
      $playlist = Engine_Api::_()->getItem('mp3music_playlist', $values['playlist_id']);
    else {
      $playlist = $this->_playlist = Engine_Api::_()->getDbtable('playlists', 'mp3music')->createRow();
      $playlist->title = trim(htmlspecialchars($values['title']));
      if (empty($playlist->title))
        $playlist->title = $translate->_('_MUSIC_UNTITLED_PLAYLIST');
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
      //$playlist->owner_type    = 'user';
      $playlist->user_id      = Engine_Api::_()->user()->getViewer()->getIdentity();
      $playlist->description   = trim(htmlspecialchars($values['description']));
      $playlist->search        = $values['search'];
      $playlist->save();
    }
    // Authorizations
    $auth      = Engine_Api::_()->authorization()->context;
    $prev_allow_comment = $prev_allow_view = false;
    foreach ($this->_roles as $role => $role_label) {
      // allow viewers
      if ($values['auth_view'] == $role || $prev_allow_view) {
        $auth->setAllowed($playlist, $role, 'view', true);
        $prev_allow_view = true;
      } else
        $auth->setAllowed($playlist, $role, 'view', 0);

      // allow comments
      if ($values['auth_comment'] == $role || $prev_allow_comment) {
        $auth->setAllowed($playlist, $role, 'comment', true);
        $prev_allow_comment = true;
      } else
        $auth->setAllowed($playlist, $role, 'comment', 0);
    }
	    // Rebuild privacy
	    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
	    foreach( $actionTable->getActionsByObject($playlist) as $action ) {
	      $actionTable->resetActivityBindings($action);
	    }
    return $playlist;
    }
}
