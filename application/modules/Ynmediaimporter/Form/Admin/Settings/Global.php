<?php

class Ynmediaimporter_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'ynmediaimporter_page', array(
      'label' => 'Number Photos/Albums Per Page',
      'description' => 'How many photos/albums will be shown per page? (Enter a number between 10 and 40). Default 20',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.page', 20),
      'validators'=>array('Int',array('Between',false,array(10,40))),
      'required'=>1,
    ));
    
    /**
     * @var int ynmediaimporter.thumbwidth {100,200}, default 165
     * @var int ynmediaimporter.thumbheight {100,200} default 116
     * @var int ynmediaimporter.wrapheight {100,250} default 180
     * @var int ynmediaimporter.wrapmargin {5,30} default 10 
     */
    
    $this->addElement('Text','ynmediaimporter_albumthumbwidth',array(
        'label'=>'Album Max Thumbnail Width',
        'description'=>'Enter a number between 100 and 200. Default: 165',
        'validators'=>array('Int',array('Between',false,array(100,200))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.albumthumbwidth', 165),  
        'required'=>1,
    ));
    
    $this->addElement('Text','ynmediaimporter_albumthumbheight',array(
        'label'=>'Album Max Thumbnail Height',
        'description'=>'Enter a number between 100 and 200. Default: 116',
        'validators'=>array('Int',array('Between',false,array(100,200))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.albumthumbheight', 116),  
        'required'=>1,
    ));
    
    $this->addElement('Text','ynmediaimporter_albumwrapheight',array(
        'label'=>'Album Thumbnail Wrapper Height',
        'description'=>'Enter a number between 150 and 300. Default: 200',
        'validators'=>array('Int',array('Between',false,array(150,300))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.albumwrapheight', 200),  
        'required'=>1,
    ));
    
    $this->addElement('Text','ynmediaimporter_albumwrapmargin',array(
        'label'=>'Album Thumbnail Wrapper Margin',
        'description'=>'Enter a number between 5 and 20. Default: 10',
        'validators'=>array('Int',array('Between',false,array(5,20))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.albumwrapmargin',10),  
        'required'=>1,
    ));
    
    
    // photo size settings
    $this->addElement('Text','ynmediaimporter_photothumbwidth',array(
        'label'=>'Photo Max Thumbnail Width',
        'description'=>'Enter a number between 100 and 200. Default: 165',
        'validators'=>array('Int',array('Between',false,array(100,200))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.photothumbwidth', 165),  
        'required'=>1,
    ));
    
    $this->addElement('Text','ynmediaimporter_photothumbheight',array(
        'label'=>'Photo Max Thumbnail Height',
        'description'=>'Enter a number between 100 and 200. Default: 116',
        'validators'=>array('Int',array('Between',false,array(100,200))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.photothumbheight', 116),  
        'required'=>1,
    ));
    
    $this->addElement('Text','ynmediaimporter_photowrapheight',array(
        'label'=>'Photo Thumbnail Wrapper Height',
        'description'=>'Enter a number between 150 and 300. Default: 160',
        'validators'=>array('Int',array('Between',false,array(150,300))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.photowrapheight', 160),  
        'required'=>1,
    ));
    
    $this->addElement('Text','ynmediaimporter_photowrapmargin',array(
        'label'=>'Photo Thumbnail Wrapper Margin',
        'description'=>'Enter a number between 5 and 20. Default: 10',
        'validators'=>array('Int',array('Between',false,array(5,20))),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.photowrapmargin',10),  
        'required'=>1,
    ));
    
    $this->addElement('Text', 'ynmediaimporter_numberphoto', array(
      'label' => 'Number Photos Per Queue',
      'description' => 'How many photos will be imported per each queue? (Enter a number between 10 and 100), suggest 20',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.numberphoto', 40),
      'validators'=>array('Int',array('Between',false,array(10,100))),
      'required'=>1,
    ));
    
    $this->addElement('Text', 'ynmediaimporter_numberqueue', array(
      'label' => 'Number Queue Per Cron',
      'description' => 'How many queue will be process per cron? (Enter a number between 10 and 200), suggest 20',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmediaimporter.numberqueue', 100),
      'validators'=>array('Int',array('Between',false, array(10,200))),
      'required'=>1,
    ));
    

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}