<?php
class Mp3music_Form_EditSinger extends Mp3music_Form_CreateSinger
{
  public $singer;
  public function init()
  {
  // Init form
    parent::init();
    $this
      ->setTitle('Edit singer information')
      ->setAttrib('id',      'form-edit-singer')
      ->setAttrib('name',    'mp3music_edit_singer')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'mp3music_edit_singer'))   
      ;
    $this->addElement('Hidden', 'singer_id'); 
    // Override submit button
    $this->removeElement('submit');
    $this->addElement('Button', 'save', array(
      'label' => 'Save Changes',
      'type' => 'submit',
       'decorators' => array(
          array('ViewScript', array(
                'viewScript' => '_formButtonCancel.tpl',
                'class'      => 'form element'
          ))
      ),
    ));
  }
 public function populate($singer)
  {
    $this->setTitle('Edit singer information');

    foreach (array(
      'singer_id' => $singer->getIdentity(),
      'title'       => htmlspecialchars_decode($singer->title),
      ) as $key => $value) {
        $this->getElement($key)->setValue($value);
    }
  }
  public function saveValues()
  { 
    $singer = parent::saveValues();
    $values   = $this->getValues();
    if ($singer) {
        $singer->title  = trim(htmlspecialchars($values['title']));
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
      if(trim($singer->title) == "")
         $singer->title = "untitled_singer";
      $singer->save();
      return $singer;
    } else {
      return false;
    }
  }
}
