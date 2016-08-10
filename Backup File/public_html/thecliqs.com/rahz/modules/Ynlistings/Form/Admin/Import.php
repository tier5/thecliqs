<?php
class Ynlistings_Form_Admin_Import extends Engine_Form {
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
	$id = $user -> level_id;
	
	// Init path
    $this->addElement('File', 'file_import', array(
      'label' => 'Select File',
      'description' => 'Choose a file XLS, CSV to import.' 
    ));
    $this->file_import->addValidator('Extension', false, 'csv,xls');	

    $this->addElement('Radio', 'approved', array(
      'label' => 'Approve Listings',
      'multiOptions' => array(
        1 => 'Approve these listings automatically',
        0 => 'These listings must wait for approval'
      ),
      'value' => 1
    ));
    
    // Privacy
    $availableLabels = array(
      'everyone' => 'Everyone',
      'registered' => 'All Registered',
      'network' => 'My Network',
      'owner_member' => 'My Friends',
      'owner' => 'Only Me',
    );

    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'view_listings');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'view', array(
            'label' => 'View Privacy',
            'description' => 'Who may view these listings?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
    // Post activities
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'comment', array(
            'label' => 'Post Activities Privacy',
            'description' => 'Who may post activities on these listings?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    
	// Share
    $shareOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'sharing');
    $shareOptions = array_intersect_key($availableLabels, array_flip($shareOptions));

    if( !empty($shareOptions) && count($shareOptions) >= 1 ) {
      // Make a hidden field
      if(count($shareOptions) == 1) {
        $this->addElement('hidden', 'share', array('value' => key($shareOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'share', array(
            'label' => 'Share Privacy',
            'description' => 'Who may share these listings?',
            'multiOptions' => $shareOptions,
            'value' => key($shareOptions),
        ));
        $this->share->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
    //Printing
    $printingOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'printing');
    $printingOptions = array_intersect_key($availableLabels, array_flip($printingOptions));

    if( !empty($printingOptions) && count($printingOptions) >= 1 ) {
      // Make a hidden field
      if(count($printingOptions) == 1) {
        $this->addElement('hidden', 'print', array('value' => key($printingOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'print', array(
            'label' => 'Print Privacy',
            'description' => 'Who may print these listings?',
            'multiOptions' => $printingOptions,
            'value' => key($printingOptions),
        ));
        $this->print->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    
	//Photo
    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'add_photos');
    $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));

    if( !empty($photoOptions) && count($photoOptions) >= 1 ) {
      // Make a hidden field
      if(count($photoOptions) == 1) {
        $this->addElement('hidden', 'upload_photos', array('value' => key($photoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'upload_photos', array(
            'label' => 'Photo Creation',
            'description' => 'Who may add photos to these listings?',
            'multiOptions' => $photoOptions,
            'value' => key($photoOptions),
        ));
        $this->upload_photos->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
    //Video
    $videoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'add_videos');
    $videoOptions = array_intersect_key($availableLabels, array_flip($videoOptions));

    if( !empty($videoOptions) && count($videoOptions) >= 1 ) {
      // Make a hidden field
      if(count($videoOptions) == 1) {
        $this->addElement('hidden', 'upload_videos', array('value' => key($videoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'upload_videos', array(
            'label' => 'Video Creation',
            'description' => 'Who may add videos to these listings?',
            'multiOptions' => $videoOptions,
            'value' => key($videoOptions),
        ));
        $this->upload_videos->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
	//Discussion
    $discussionOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynlistings_listing', $id, 'add_discussions');
    $discussionOptions = array_intersect_key($availableLabels, array_flip($discussionOptions));

    if( !empty($discussionOptions) && count($discussionOptions) >= 1 ) {
      // Make a hidden field
      if(count($discussionOptions) == 1) {
        $this->addElement('hidden', 'discussion', array('value' => key($discussionOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'discussion', array(
            'label' => 'Discussion Creation',
            'description' => 'Who may add discussions to these listings?',
            'multiOptions' => $discussionOptions,
            'value' => key($discussionOptions),
        ));
        $this->discussion->getDecorator('Description')->setOption('placement', 'append');
      }
    }


    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Import Listings',
      'type'  => 'button',
      'onclick' => 'import_listings()'
    ));
  }

}
