<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SuggestOptions.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_View_Helper_SuggestOptions extends Zend_View_Helper_Abstract
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
  public function suggestOptions(Core_Model_Item_Abstract $suggest)
  {
    $html = '<div class="options">';
    $object = $suggest->getObject();
    $type = $object->getType();
    $id = $object->getIdentity();
    $or = $this->view->translate('or');
    $label = $this->view->translate("suggest_view_this_".$type);
    
    switch ($type) {
      case 'group':
      case 'event':
        $url = $this->view->url(array(
            'controller' => 'member',
            'action' => 'join',
            $type.'_id' => $id
          ), $type.'_extended');
        
        $params = array('class' => 'smoothbox button', 'style' => 'float: left;');
      break;
      case 'user':
        $url = $this->view->url(array(
            'controller' => 'friends',
            'action' => 'add',
              'user_id' => $id
          ), 'user_extended');
        
        $params = array('class' => 'smoothbox button');
      break;
      case 'suggest_profile_photo':
        $url = $this->view->url(array(                    
          'action' => 'profile-photo',
          'photo_id' => $object->getIdentity(),
          'format' => 'smoothbox'), 'suggest_general');
        $params = array('class' => 'smoothbox button');
      break;
      default:
        $url = $this->view->url(array(
            'controller' => 'index',
            'action' => 'accept-suggest',
            'object_type' => $type,
            'object_id' => $id,
          ), 'suggest_general');

        $params = array('class' => 'button', 'target' => '_blank');
      break;
    }

    $link = $this->view->htmlLink($url, $label, $params);
    $cancel = $this->view->htmlLink($this->view->url(array(
        'action' => 'index',
        'controller' => 'index',
        'suggest_id' => $suggest->getIdentity()
      ), 'suggest_general'),
      $this->view->translate('suggest_cancel_suggest_'.$type),
      array(
        'class' => 'button disabled'
      )
    );

    $html .= '<div class="accept">'.$link.'</div>';
    $html .= '<div class="or">'.$or.'</div>';
    $html .= '<div class="cancel">'.$cancel.'</div>';
    $html .= '<div class="clr"></div></div>';

    return $html;
  }

}