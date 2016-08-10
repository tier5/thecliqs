<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_ProfileMapController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $page_id = $subject->getIdentity();
    
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $table
      ->select()->setIntegrityCheck(false)
      ->from(array('page' => 'engine4_page_pages'))
      ->joinLeft(array('marker' => 'engine4_page_markers'), 'marker.page_id = page.page_id', array('marker_id', 'latitude', 'longitude'))
      ->where('page.page_id = ?', $page_id);

    $page = $table->fetchRow($select);
   	$markers = array();
    if ($page->marker_id > 0) {
	    $markers[0] = array(
				'marker_id' => $page->marker_id,
				'lat' => $page->latitude,
				'lng' => $page->longitude,
				'pages_id' => $page->page_id,
				'pages_photo' => $page->getPhotoUrl('thumb.normal'),
				'title' => $page->getTitle(),
				'desc' => Engine_String::substr($page->getDescription(),0,200),
	      'url' => $page->getHref()
			);
			
			$this->view->markers = Zend_Json_Encoder::encode($markers);
			$this->view->bounds = Zend_Json_Encoder::encode(Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers));
    } else {
      return $this->setNoRender();
    }
  }
}