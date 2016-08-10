<?php
class Yncontest_Form_Entries_Search extends Engine_Form {
  public function init()
  {
    $this->setAttribs(array(
                 'id' => 'filter_form',
                 'class' => 'global_form_box',
                 'method'=>'POST',
             ));

    //Search Title
    //$this->addElement('Text', 'entry_id', array(
    //  'label' => 'Entry_ID',
   // ));
    $this->addElement('Text', 'entry_name', array(
    		'label' => 'Entry\'s Name',
    ));
       
	$plugin = Engine_Api::_() -> yncontest() -> getPlugins();    
	//$contest_type =array_merge(array('0' => 'All'),$plugin);	
    
	$this->addElement('Select', 'entry_type', array(
      'label' => "Entry's type",
      'multiOptions' => $plugin,           
    ));
		
	//Feature Filter
    $this->addElement('Select', 'approve_status', array(
      'label' => 'Entry Status',
      'multiOptions' => array(  
      	'all' => 'all',        
      	'approved' => 'Approved',
      	'pending' => 'Pending',
      	'denied' => 'Denied',         
    ),
      'value' => 'all',
      //'onchange' => 'this.form.submit();',
    ));
		
    //Feature Filter
//     $this->addElement('Select', 'filter', array(
//       'label' => 'Filter By',
//       'multiOptions' => array(
//         'start_date' => 'Most Recent',
//         'view_count' => 'Most Viewed',
//         'follow_count' => 'Most Followed',
//         'vote_count' => 'Most Vote',
//         'like_count' => 'Most Liked',
//         'comment_count' => 'Most Commented',
//     ),
//       'value' => '',
//       'onchange' => 'this.form.submit();',
//     ));

     // Element: order
    $this->addElement('Hidden', 'orderby', array(
      'order' => 101,
      'value' => 'start_date'
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 102,
      'value' => 'DESC',
    ));

     // Element: direction
    $this->addElement('Hidden', 'page', array(
      'order' => 103,
    ));

     // Buttons
    $this->addElement('Button', 'button', array(
      'label' => 'Search',
      'type' => 'submit',
    ));
  }
}