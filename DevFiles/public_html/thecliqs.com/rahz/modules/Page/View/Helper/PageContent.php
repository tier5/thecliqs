<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageContent.php 16.12.11 16:23 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_View_Helper_PageContent extends Zend_View_Helper_Abstract
{
  /**
   * Name of current area
   * 
   * @var string
   */
  protected $_name;

  /**
   * Render a content area by name
   * 
   * @param string $name
   * @return string
   */
  public function pageContent($name = null)
  {
    // Direct access
    if( func_num_args() == 0 )
    {
      return $this;
    }

    if( func_num_args() > 1 )
    {
      $name = func_get_args();
    }

    $content = Engine_Content::getInstance();
		$content->setStorage(Engine_Api::_()->getDbTable('pages', 'core'));
    $rendered = $content->render($name);
		
		$content->setStorage(Engine_Api::_()->getDbTable('pages', 'page'));
		return $rendered;
  }

  public function renderWidget($name)
  {
    $structure = array(
      'type' => 'widget',
      'name' => $name,
    );

    // Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
      'elements' => array($structure),
      'decorators' => array(
        'Children'
      )
    ));

    return $element->render();
  }
}