<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Suggest.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Model_Suggest extends Core_Model_Item_Abstract
{

  protected $_from = null;

  protected $_object = null;

  protected $_to = null;

  protected $_tos = null;

  protected $_froms = null;

  public function getHref($params = array())
  {
		 $params = array_merge(array(
       'route' => 'suggest_view',
       'reset' => true,
       'suggest_id' => $this->suggest_id,
       ), $params
     );

	   $route = $params['route'];
	   $reset = $params['reset'];
	   unset($params['route']);
	   unset($params['reset']);

	   return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getObjectHref($params = array())
  {
		 $params = array_merge(array(
       'route' => 'suggest_general',
       'reset' => true,
       'object_type' => $this->object_type,
       'object_id' => $this->object_id,
       'action' => 'accept-suggest',
       'controller' => 'index'
       ), $params
     );

	   $route = $params['route'];
	   $reset = $params['reset'];
	   unset($params['route']);
	   unset($params['reset']);

	   return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getObject()
  {
    if ($this->_object !== null) {
      return $this->_object;
    }

    if (!$this->object_type || !$this->object_id) {
      return false;
    }

    $this->_object = Engine_Api::_()->getItem($this->object_type, $this->object_id);
    return $this->_object;
  }

  public function getFrom()
  {
    if ($this->_from !== null) {
      return $this->_from;
    }

    if (!$this->from_id) {
      return false;
    }

    $this->_from = Engine_Api::_()->getItem('user', $this->from_id);
    return $this->_from;
  }

  public function getTo()
  {
    if ($this->_to !== null) {
      return $this->_to;
    }

    if (!$this->to_id) {
      return false;
    }

    $this->_to = Engine_Api::_()->getItem('user', $this->to_id);
    return $this->_to;
  }

  public function getFroms()
  {
    if ($this->_froms !== null) {
      return $this->_froms;
    }

    $table = $this->getTable();
    $name = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name, 'from_id')
      ->where('to_id = ?', $this->to_id)
      ->where('object_type = ?', $this->object_type)
      ->where('object_id = ?', $this->object_id);

    $ids = $table->getAdapter()->fetchCol($select);
    $this->_froms = Engine_Api::_()->getItemMulti('user', $ids);
    return $this->_froms;
  }

  public function getTos()
  {
    if ($this->_tos !== null) {
      return $this->_tos;
    }

    $table = $this->getTable();
    $name = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name, 'to_id')
      ->where('from_id = ?', $this->from_id)
      ->where('object_type = ?', $this->object_type)
      ->where('object_id = ?', $this->object_id);

    $ids = $table->getAdapter()->fetchCol($select);
    $this->_tos = Engine_Api::_()->getItemMulti('user', $ids);
    return $this->_tos;
  }

  public function getDescription()
  {
    $view = Zend_Registry::get('Zend_View');

    $object = $this->getObject();
    $froms = $this->getFroms();

    $html = '';
    $etc = array();
    if (isset($object->view_count) && $object->view_count > 0) {
      $etc[] = $view->translate(array('%s view', '%s views', $object->view_count), $object->view_count);
    }

    if (isset($object->comment_count) && $object->comment_count > 0) {
      $etc[] = $view->translate(array('%s comment', '%s comments', $object->comment_count), $object->comment_count);
    }

    if (isset($object->member_count) && $object->member_count > 0) {
      $etc[] = $view->translate(array('%s member', '%s members', $object->member_count), $object->member_count);
    }

    $html .= implode(', ', $etc);
    if (count($etc)) {
      $html .= '<br /><br />';
    }

    $links = array();
    foreach ($froms as $from) {
      $links[] = $view->htmlLink($from->getHref(), $from->getTitle(), array('class' => 'bold'));
    }
    $link = implode(', ', $links);

    $date = $view->timestamp($this->suggest_date);
    $html .= $view->translate('suggest_description_'.$this->object_type, $link, $date);

    return $html;
  }

  public function getTitle()
  {
    return $this->getObject()->getTitle();
  }

  public function __toString()
  {
    $view = Zend_Registry::get('Zend_View');
		$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

    switch($this->object_type){
      case 'playlist':
        $object_type = 'pagemusic';
        break;
      case 'pagediscussion_pagepost':
        $object_type = 'pagediscussion';
        break;
      case 'ynmusic_album':
        $object_type = 'ynmusic';
        break;
      case 'avp_video':
        $object_type = 'avp';
        break;
      case 'artarticle':
        $object_type = 'advancedarticles';
        break;
      case 'list_listing':
        $object_type = 'list';
        break;
      default:
        $object_type = $this->object_type;
    }

    if (!Engine_Api::_()->suggest()->checkItemModule($object_type)) {
      return '';
    }

    if (!$this->getObject()) {
      return '';
    }

    $object = $this->getObject();
    if (!$object->getIdentity()) {
      return '';
    }

    $likeEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like');
    $thumb = 'thumb.icon';

    if ($object->getType() == 'suggest_profile_photo') {
      return $view->partial('suggest/photo.tpl', 'suggest', array(
        'object' => $object,
        'action' => $action,
        'thumb' => null,
        'suggest' => $this,
        'likeEnabled' => $likeEnabled
      ));
    }
    return $view->partial('suggest/item.tpl', 'suggest', array(
      'object' => $object,
      'action' => $action,
      'thumb' => $thumb,
      'suggest' => $this,
      'likeEnabled' => $likeEnabled
    ));    
  }

  public function getParent()
  {
    return null;
  }

  public function isSearchable()
  {
    return false;
  }

}