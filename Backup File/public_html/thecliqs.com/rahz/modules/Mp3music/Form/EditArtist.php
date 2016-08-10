<?php
class Mp3music_Form_EditArtist extends Mp3music_Form_CreateArtist
{
  public $artist;
  public function init()
  {
  // Init form
    parent::init();
    $this
      ->setTitle('Edit artist information')
      ->setAttrib('id',      'form-edit-artist')
      ->setAttrib('name',    'mp3music_edit_artist')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'mp3music_edit_artist'))   
      ;
    $this->addElement('Hidden', 'artist_id'); 
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
 public function populate($artist)
  {
    $this->setTitle('Edit artist information');

    foreach (array(
      'artist_id' => $artist->getIdentity(),
      'title'       => $artist->title,
      ) as $key => $value) {
        $this->getElement($key)->setValue($value);
    }
  }
  public function saveValues()
  { 
    $artist = parent::saveValues();
    $values   = $this->getValues();
    if ($artist) {
        $artist->title  = $values['title'];
      if(trim($artist->title) == "")
         $artist->title = "untitled_artist";
      $artist->save();
      return $artist;
    } else {
      return false;
    }
  }
}
