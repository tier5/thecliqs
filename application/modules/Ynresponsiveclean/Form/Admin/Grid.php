<?php
class Ynresponsiveclean_Form_Admin_Grid extends Engine_Form{
  function init()
  {
    $this->addElement('select', 'content_type', array(
      'label'=>'Content Type',
      'required'=>true,
      'multiOptions'=> Engine_Api::_()->ynresponsive1()->getAllSupportedSliderContent()
    ));  
    
    $this->addElement('select','col_style',array(
      'label'=>'Grid Style',
      'required'=>true,
      'multiOptions'=>array(
        1=>'Poster photo at left side',
        2=>'Large item\'s at top',
      )
    ));
    
    $this->addElement('select','show_title', array('label'=>'Show Title', 'value'=>'1', 'multiOptions'=>array('1'=>'Yes','0'=>'No')));
    $this->addElement('select','show_description', array('label'=>'Show Description', 'value'=>'1', 'multiOptions'=>array('1'=>'Yes','0'=>'No')));
    $this->addElement('select','show_readmore', array('label'=>'Show Read More', 'value'=>'1', 'multiOptions'=>array('1'=>'Yes','0'=>'No')));
    $this->addElement('select','num_cols', array(
      'label'=> 'Number Of Columns',
      'value'=>1,
      'multiOptions'=>array('1'=>'1 Column','2'=>'2 Columns','3'=>'3 Columns','4'=>'4 Columns'),
    ));
    $this->addElement('select','num_rows', array(
      'label'=> 'Number Of Rows',
      'value'=>1,
      'multiOptions'=>array('1'=>1,'2'=>2,'3'=>3,'4'=>4),
    ));
  }
}
