<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Page Global Settings')
      ->setDescription('PAGE_FORM_ADMIN_GLOBAL_DESCRIPTION');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->loadDefaultDecorators();
    /*
    $this->addElement('Text', 'gmap_key', array(
      'label' => 'Google Map Key',
      'description' => 'PAGE_SETTING_GMAPKEY_DESC',
      'value' => $settings->getSetting('page.gmapkey', ''),
      'size' => 60
    ));

    $this->getElement('gmap_key')->getDecorator('Description')->setOption('escape', false);
    */
    
    $this->addElement('Text', 'browse_page_count', array(
      'label' => 'Item Count on Browse Page',
      'description' => 'PAGE_SETTING_BROWSE_COUNT',
      'value' => $settings->getSetting('page.browse_count', 10)
    ));

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')) {
      $this->addElement('Text', 'rate_browse_reviews_count', array(
        'label' => 'Reviews Count on Browse Reviews',
        'description' => 'RATE_SETTING_BROWSE_REVIEWS_COUNT_DESCRIPTION',
        'value' => $settings->getSetting('rate.browse.reviews.count', 5)
      ));
    }
    
    $this->addElement('Text', 'recent_page_count', array(
      'label' => 'Recent Pages Count',
      'description' => 'PAGE_SETTING_RECENT_COUNT',
      'value' => $settings->getSetting('page.recent_count', 6)
    ));
    
    $this->addElement('Text', 'popular_page_count', array(
      'label' => 'Popular Pages Count',
      'description' => 'PAGE_SETTING_POPULAR_COUNT',
      'value' => $settings->getSetting('page.popular_count', 6)
    ));
    
    $this->addElement('Text', 'featured_page_count', array(
      'label' => 'Featured Pages Count',
      'description' => 'PAGE_SETTING_FEATURED_COUNT',
      'value' => $settings->getSetting('page.featured_count', 6)
    ));

    $this->addElement('Text', 'sponsored_page_count', array(
      'label' => 'Sponsored Pages Count',
      'description' => 'PAGE_SETTING_SPONSORED_COUNT',
      'value' => $settings->getSetting('page.sponsored_count', 6)
    ));

    $this->addElement('Text', 'page_abc', array(
      'label' => 'Page Abc symbols',
      'description' => 'PAGE_SETTING_ABC',
      'value' => $settings->getSetting('page.abc', 'A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z')
    ));

    $this->addElement('Checkbox', 'default_package', array(
      'description' => 'DEFAULT_PACKAGE_ENABLED_DESCRIPTION',
      'label' => 'Enable Default Package',
      'decorators' => array(
        'ViewHelper',
        array('Description', array('placement' => 'PREPPEND', 'class' => 'package-description')),
        array('Label', array('placement' => 'PREPPEND', 'tag' => 'div', 'class' => 'form-label')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
      ),
      'value' => $settings->getSetting('default.package.enabled', 1),
    ));

    $this->addElement('Radio', 'browse_mode', array(
      'label' => 'Browse Mode',
      'description' => 'Choose which mode do you want to see in browse page by default',
      'multiOptions' => array(
        'list' => 'List Mode',
        'icons' => 'Icon Mode',
        'map' => 'Map Mode',
      ),
      'value' => $settings->getSetting('page.browse.mode', 'list'),
    ));

    $this->addElement('Radio', 'adv_search_unit', array(
      'label' => 'Preferred unit system for displaying distance',
      'description' => 'Choose which unit do you want to use in "Advanced Search" and "Browse Locations" widgets',
      'multiOptions' => array(
        'Miles' => 'Miles',
        'Km' => 'Km (Kilometers)',
      ),
      'value' => $settings->getSetting('page.advsearch.unit','Miles'),
    ));

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    if ($modulesTbl->isModuleEnabled('offers')) {
      $this->addElement('Checkbox', 'sort_pages_active_offers', array(
        'description' => 'SORT_PAGES_BY_ACTIVE_OFFERS_DESCRIPTION',
        'label' => 'Sort Pages By Active Offers',
        'decorators' => array(
          'ViewHelper',
          array('Description', array('placement' => 'PREPPEND', 'class' => 'package-description')),
          array('Label', array('placement' => 'PREPPEND', 'tag' => 'div', 'class' => 'form-label')),
          array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
        ),
        'value' => $settings->getSetting('page.sort.active.offers', 1),
      ));
    }

    if ($modulesTbl->isModuleEnabled('communityad')) {
      $this->addElement('Checkbox', 'communityad', array(
        'description' => 'If enabled, advertisements will be shown on left side of profile page. Page owners can\'t remove the widget.',
        'label' => 'Community Ads Integration with Pages Plugin (not Timeline)',
        'decorators' => array(
          'ViewHelper',
          array('Description', array('placement' => 'PREPPEND', 'class' => 'package-description')),
          array('Label', array('placement' => 'PREPPEND', 'tag' => 'div', 'class' => 'form-label')),
          array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
        ),
        'value' => $settings->getSetting('page.communityad.enabled', 0),
      ));
    }

    if ($modulesTbl->isModuleEnabled('rate')) {
      $this->addElement('Checkbox', 'browse_reviews_enable', array(
        'description' => 'If enabled, the last reviews of every page will be shown.',
        'label' => 'Display Browse Reviews',
        'decorators' => array(
          'ViewHelper',
          array('Description', array('placement' => 'PREPPEND', 'class' => 'package-description')),
          array('Label', array('placement' => 'PREPPEND', 'tag' => 'div', 'class' => 'form-label')),
          array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
        ),
        'value' => $settings->getSetting('rate.browse.reviews.enable', 0),
      ));
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}