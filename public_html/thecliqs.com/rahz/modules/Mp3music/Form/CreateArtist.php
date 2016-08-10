<?php
class Mp3music_Form_CreateArtist extends Engine_Form
{
  public $artist;
  public function init()
  {
    // Init form
    $this
      ->setTitle('Create artist information')
      ->setAttrib('id',      'form-create-artist')
      ->setAttrib('name',    'mp3music_create_artist')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'mp3music_create_artist'))
      ;
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Artist Name',
      'maxlength' => '128',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '128')),
      )
    ));
    // Init singer art
     $this->addElement('File', 'art', array(
      'label' => 'Artist Photo',
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
    $artist = null;
    $values   = $this->getValues();
    $translate= Zend_Registry::get('Zend_Translate');
    if(!empty($values['artist_id']))
      $artist = Engine_Api::_()->getItem('mp3music_artist', $values['artist_id']);
    else {
      $artist = Engine_Api::_()->getDbtable('artists', 'mp3music')->createRow();
      $artist->title = trim($values['title']);
      $artist->save();
      $values['artist_id']   = $artist->artist_id;
    }
    if (!empty($values['art']))
      $artist->setPhoto($this->art);
    return $artist;
  }
}
