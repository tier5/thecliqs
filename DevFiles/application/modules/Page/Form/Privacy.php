<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Privacy.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Privacy extends Engine_Form
{
	protected $_parent_type;
  protected $_parent_id;
	protected $_page;
  protected $_isOwner;

	public function __construct($options){

		$this->_page = $options['page'];
    $this->_isOwner = $this->_page->isOwner(Engine_Api::_()->user()->getViewer());
		parent::__construct($options);
	}
  public function setParent_type($value)
  { 
    $this->_parent_type = $value;
  }

  public function setParent_id($value)
  {
    $this->_parent_id = $value;
  }

	public function init()
  {
    $this->setTitle('Privacy Settings')
      ->setDescription('Edit your Page privacy settings: who can view, post content, etc.')
    ;

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

  	$user = Engine_Api::_()->user()->getViewer();

  	$this->addElement('Radio', 'search', array(
  		'label' => 'Search Privacy',
      'description' => 'Include this page in search results?',
      'value' => 1,
  		'multiOptions' => array(
  				1 => 'Yes, include in search results.',
  				0 => 'No, hide from search results.'
  		),
      'value' => $this->_page->search,
    ));

    if( !$this->_isOwner ) {
      $this->search->setAttrib('disabled', true);
    }
    
    $availableLabels = array(
      'everyone' => 'Everyone',
      'registered' => 'Registered Members',
      'likes' => 'Likes, Admins and Owner',
      'team' => 'Admins and Owner Only',
    );

		if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.package.enabled', 0) && $this->_page instanceof Page_Model_Page) {
			/**
			 * @var $page Page_Model_Package
			 */
			$package = $this->_page->getPackage();

			$view_options = $package->auth_view;
			$package->auth_comment;
			$package->auth_posting;
		} else {
			$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_view');
		}

		$view_options = array_intersect_key($availableLabels, array_flip($view_options));

    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_comment');
		$comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

    $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_posting');
		$posting_options = array_intersect_key($availableLabels, array_flip($posting_options));

  	if (!empty($view_options)) {
      $this->addElement('Radio', 'auth_view', array(
        'label' => 'View Privacy',
        'description' => 'Who can see this page?',
        'multiOptions' => $view_options,
        'value' => key($view_options),
      ));

      if( !$this->_isOwner ) {
        $this->auth_view->setAttrib('disabled', true);
      }
    }

    if (!empty($comment_options)){
      $this->addElement('Radio', 'auth_comment', array(
        'label' => 'Comment Privacy',
        'description' => 'Who can comment photos, videos, blogs and events?',
        'multiOptions' => $comment_options,
        'value' => key($comment_options),
      ));
      if( !$this->_isOwner ) {
        $this->auth_comment->setAttrib('disabled', true);
      }
    }

    // Applications privacy settings
    $this->addApplicationsPrivacy($posting_options);

    if( $this->_isOwner ) {
      // Element: execute
      $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
          'ViewHelper',
        ),
      ));

      // Element: cancel
      $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'page_manage', true),
        'decorators' => array(
          'ViewHelper'
        )
      ));

      // DisplayGroup: buttons
      $this->addDisplayGroup(array(
        'execute',
        'cancel',
      ), 'buttons', array(
        'decorators' => array(
          'FormElements',
          'DivDivDivWrapper'
        ),
      ));
    }

  }

  private function addApplicationsPrivacy($posting_options)
  {
    $api = Engine_Api::_()->getApi('core', 'page');
    $pageExtensions = array();
    $page_features = $this->_page->getAllowedFeatures();

    if( $api->isModuleExists('pagealbum') &&  in_array('pagealbum', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_album_posting', array(
          'label' => 'Page Album Posting Privacy',
          'description' => 'Who can post photos?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_album_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_album_posting';
      }
    }

    if( $api->isModuleExists('pageblog') &&  in_array('pageblog', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_blog_posting', array(
          'label' => 'Page Blog Posting Privacy',
          'description' => 'Who can post blogs?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_blog_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_blog_posting';
      }
    }

    if( $api->isModuleExists('pagediscussion') &&  in_array('pagediscussion', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_disc_posting', array(
          'label' => 'Page Discussion Posting Privacy',
          'description' => 'Who can post discussions?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_disc_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_disc_posting';
      }
    }

    if( $api->isModuleExists('pagedocument')  &&  in_array('pagedocument', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_doc_posting', array(
          'label' => 'Page Document Posting Privacy',
          'description' => 'Who can post documents?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_doc_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_doc_posting';
      }
    }

    if( $api->isModuleExists('pageevent') &&  in_array('pageevent', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_event_posting', array(
          'label' => 'Page Event Posting Privacy',
          'description' => 'Who can post events?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_event_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_event_posting';
      }
    }

    if( $api->isModuleExists('pagemusic') &&  in_array('pagemusic', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_music_posting', array(
          'label' => 'Page Music Posting Privacy',
          'description' => 'Who can post playlists?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_music_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_music_posting';
      }
    }

    if( $api->isModuleExists('pagevideo') &&  in_array('pagevideo', $page_features) ) {
      if (!empty($posting_options)) {
        $this->addElement('Radio', 'auth_video_posting', array(
          'label' => 'Page Video Posting Privacy',
          'description' => 'Who can post videos?',
          'multiOptions' => $posting_options,
          'value' => key($posting_options),
        ));
        if( !$this->_isOwner ) {
          $this->auth_video_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_video_posting';
      }
    }

    if( $this->_page->isStore() &&  in_array('store', $page_features) ) {
      $this->addElement('Radio', 'auth_store_posting', array(
        'label' => 'Store Posting Privacy',
        'description' => 'Who can post Store Products?',
        'multiOptions' => array(
          'team' => 'Admins and Owner',
          'owner' => 'Owner only',
        ),
        'value' => key( array(
          'team' => 'Admins and Owner',
          'owner' => 'Owner only',
        )),
      ));
      if( !$this->_isOwner ) {
        $this->auth_store_posting->setAttrib('disabled', true);
      }
      $pageExtensions[] = 'auth_store_posting';

    }

    // Donation privacy
    if($this->_page->isDonation() && in_array('donation',$page_features)){
      $settings = Engine_Api::_()->getApi('settings', 'core');
      if($settings->getSetting('donation.enable.charities',1)){
        // Charity privacy
        $this->addElement('Radio', 'auth_charity_posting', array(
          'label' => 'Charity Posting Privacy',
          'description' => 'Who can post Charities?',
          'multiOptions' => array(
            'team' => 'Admins and Owner',
            'owner' => 'Owner only',
          ),
        ));
        if(!$this->_isOwner){
          $this->auth_charity_posting->setAttrib('disabled', true);
        }
        $pageExtensions[] = 'auth_charity_posting';
      }
      if($settings->getSetting('donation.enable.projects',1)){
        // Project privacy
        $this->addElement('Radio','auth_project_posting', array(
          'label' => 'Project Posting Privacy',
          'description' => 'Who can post Projects?',
          'multiOptions' => array(
            'team' => 'Admins and Owner',
            'owner' => 'Owner only',
          ),
        ));
        if(!$this->_isOwner){
          $this->auth_project_posting->setAttrib('disabled',true);
        }
        $pageExtensions[] = 'auth_project_posting';
      }
    }
    if( count($pageExtensions)) {
      $this->addDisplayGroup(
        $pageExtensions,
        'extensions',
        array('legend' => '')
      );
    }

  }
}