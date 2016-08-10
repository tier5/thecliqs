<?php
class Mp3music_Form_CreateSinger extends Engine_Form
{
  public $singer;
  public function init()
  {
    // Init form
    $this
      ->setTitle('Create singer information')
      ->setAttrib('id',      'form-create-singer')
      ->setAttrib('name',    'mp3music_create_singer')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'mp3music_create_singer'))
      ;
      $singertype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('singertype_id');
       $this->addElement('Hidden', 'singertype_id', array(
      'value' => $singertype_id,
    ));
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Singer Name',
      'maxlength' => '63',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
    // Init singer art
     $this->addElement('File', 'art', array(
      'label' => 'Singer Photo',
    ));
    $this->art->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    // Init submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Add New',
      'type' => 'submit',
            'decorators' => array(
          array('ViewScript', array(
                'viewScript' => '_formButtonCancel.tpl',
                'class'      => 'form element'
          ))
      ),
    ));
  }

  public function saveValues()
  { 
    $singer = null;
    $values   = $this->getValues();
    $translate= Zend_Registry::get('Zend_Translate');
    if(!empty($values['singer_id']))
      $singer = Engine_Api::_()->getItem('mp3music_singer', $values['singer_id']);
    else {
      $singer = Engine_Api::_()->getDbtable('singers', 'mp3music')->createRow();
      $singer->title = trim(htmlspecialchars($values['title']));
      if (empty($singer->title))
        $singer->title = "untitled_singer";
      $str = $singer->title;
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
      $singer->title_url = trim(htmlspecialchars($str));
      $singer->singer_type = $values['singertype_id'];
      $singer->save();
      $values['singer_id']   = $singer->singer_id;
      // Assign $album to a Core_Model_Item
      $singer = Engine_Api::_()->getItem('mp3music_singer', $values['singer_id']);
    }
    if (!empty($values['art']))
      $singer->setPhoto($this->art);
    return $singer;
  }
}
