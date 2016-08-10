<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Admin_Level extends Engine_Form
{
  protected $_roles = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'likes' => 'Fans, Admins and Owner',
        'team' => 'Admins and Owner'
      );

  public function init()
  {
    $this
      ->setTitle('Page Level Settings')
      ->setDescription('PAGE_FORM_ADMIN_LEVEL_DESCRIPTION');

    $levels = array();
    $table  = Engine_Api::_()->getDbtable('levels', 'authorization');
    
    foreach ($table->fetchAll($table->select()->where('level_id <> 5')) as $row){
      $levels[$row['level_id']] = $row['title'];
    }
    
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels,
    ));

    $this->addElement('Radio', 'create', array(
      'label' => 'Allow Page Creation?',
      'description' => 'PAGE_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow create pages.',
        1 => 'Yes, allow create pages.'       
      ),
      'value' => 1,
    ));
    
    $this->addElement('Checkbox', 'auto_approve', array(
      'label' => 'PAGE_SETTING_APPROVAL',
      'description' => 'New Pages Approval',
      'value' => 1
    ));


    // Element:sponsored
    $this->addElement('Checkbox', 'sponsored', array(
            'description' => "PACKAGE_EDITCREATE_FORM_SPONSORED_TITLE",
            'label' => 'PACKAGE_EDITCREATE_FORM_SPONSORED_DESCRIPTION',
            'value' => 0,
    ));

    // Element:featured
    $this->addElement('Checkbox', 'featured', array(
            'description' => "PACKAGE_EDITCREATE_FORM_FEATURED_TITLE",
            'label' => 'PACKAGE_EDITCREATE_FORM_FEATURED_DESCRIPTION',
            'value' => 0,
    ));
    
    $this->addElement('Radio', 'edit_cols', array(
      'label' => 'PAGE_ALLOW_COLS_EDIT_FORM_TITLE',
      'description' => 'PAGE_ALLOW_COLS_EDIT_FORM_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow edit columns.',
        1 => 'Yes, allow edit columns.'
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'layout_editor', array(
      'label' => 'PAGE_ALLOW_LAYOUT_FORM_TITLE',
      'description' => 'PAGE_ALLOW_LAYOUT_FORM_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow layout editor.',
        1 => 'Yes, allow layout editor.'
      ),
      'value' => 1,
    ));


    $modules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $features = array();
    $features[0] = 'pagealbum';
    $features[1] = 'pageblog';
    $features[2] = 'pagediscussion';
    $features[3] = 'pagedocument';
    $features[4] = 'pageevent';
    $features[5] = 'pagemusic';
    $features[6] = 'pagevideo';
    $features[7] = 'rate';
		$features[8] = 'pagecontact';
    $features[9] = 'store';
    $features[10] = 'pagefaq';
    $features[11] = 'donation';
    $features[12] = 'offers';

    $names = array();
    $names[0] = 'Album';
    $names[1] = 'Blog';
    $names[2] = 'Discussion';
    $names[3] = 'Documents';
    $names[4] = 'Event';
    $names[5] = 'Music';
    $names[6] = 'Video';
    $names[7] = 'Rate';
		$names[8] = 'Contact';
    $names[9] = 'Store';
    $names[10] = 'FAQ';
    $names[11] = 'Donation';
    $names[12] = 'Offers';

$multiOptions = array();
    for($i=0; $i<count($features); $i++)
    {
      if(in_array($features[$i], $modules))
        $multiOptions[$features[$i]] = $names[$i];
    }

    $this->addElement('Text', 'allowed_pages', array(
      'label' => 'PAGE_MAXIMUM_ALLOWED_FORM_TITLE',
      'description' => 'PAGE_MAXIMUM_ALLOWED_FORM_DESCRIPTION'
    ));

    $this->addElement('MultiCheckbox', 'auth_features', array(
      'label' => 'Page Features Privacy',
      'description' => 'Your members can choose from any of the features checked below when they decide who can use its on their pages.',
      'multiOptions' => $multiOptions,
      'value' => array('pagealbum', 'pageblog','pagediscussion', 'pagedocument', 'pageevent', 'pagemusic', 'pagevideo', 'rate', 'pagecontact', 'store', 'pagefaq', 'donation', 'offers')
    ));

    $this->addElement('MultiCheckbox', 'auth_view', array(
      'label' => 'Page Privacy',
      'description' => 'Your members can choose from any of the options checked below when they decide who can see their pages.',
      'multiOptions' => array(
        'everyone' => 'Everyone',
        'registered' => 'Registered',
        'likes' => 'Fans',
        'team' => 'Team'
      ),
      'value' => array('everyone', 'registered','likes', 'team')
    ));
    
    $this->addElement('MultiCheckbox', 'auth_comment', array(
      'label' => 'Page Comment Options',
      'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their pages.',
      'multiOptions' => array(
        'registered' => 'Registered',
        'likes' => 'Fans',
        'team'  => 'Team'
      ),
      'value' => array('registered','likes', 'team')
    ));
    
    $this->addElement('MultiCheckbox', 'auth_posting', array(
      'label' => 'Page Posting Options',
      'description' => 'Your members can choose from any of the options checked below when they decide who can post content on their pages.',
      'multiOptions' => array(
        'registered' => 'Registered',
        'likes' => 'Fans',
        'team'  => 'Team'
      ),
      'value' => array('registered','likes', 'team')
    ));

    // Element: style
    $this->addElement('Radio', 'style', array(
      'label' => 'Allow Custom CSS Styles?',
      'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their pages by altering their CSS styles.',
      'multiOptions' => array(
        1 => 'Yes, enable custom CSS styles.',
        0 => 'No, disable custom CSS styles.',
      ),
      'value' => 1,
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}