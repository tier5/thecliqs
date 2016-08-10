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

class Suggest_View_Helper_SuggestDetails extends Zend_View_Helper_Abstract
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
  public function suggestDetails($item)
  {
    if (!($item instanceof Core_Model_Item_Abstract)) {
      return '';
    }
    
    $owner = $item->getOwner();
    $type = $item->getType();
    $id = $item->getIdentity();
    $num = rand(0, 10000);
    $html = '';

    $labelOne = '';
    $labelMany = '';
    $arg = 0;
    $params = array();

    switch ($type) {
      case 'group':
        $labelOne = 'suggest_%s member led by %s';
        $labelMany = 'suggest_%s members led by %s';
        $link = $this->view->htmlLink($owner->getHref(), $owner->getTitle(), array('id' => $num.'_suggest_'.$type.$id.'_owner_'.$owner->getGuid(), 'class' => 'he_tip_link'));
        $params = array(
          $item->member_count,
          $link
        );
        $arg = $item->member_count;
      break;
      case 'event':
        $html = $this->view->timestamp($item->starttime).' | ';
        $labelOne = 'suggest_%s guest led by %s';
        $labelMany = 'suggest_%s guests led by %s';
        $link = $this->view->htmlLink($owner->getHref(), $owner->getTitle(), array('id' => $num.'_suggest_'.$type.$id.'_owner_'.$owner->getGuid(), 'class' => 'he_tip_link'));
        $params = array(
          $item->member_count,
          $link
        );
        $arg = $item->member_count;
      break;
      case 'user':
        return '';
      break;
      case 'music_playlist':
        $labelOne = 'suggest_%s vote | posted by %s';
        $labelMany = 'suggest_%s votes | posted by %s';
        $link = $this->view->htmlLink($owner->getHref(), $owner->getTitle(), array('id' => $num.'_suggest_'.$type.$id.'_owner_'.$owner->getGuid(), 'class' => 'he_tip_link'));
        if (isset($item->play_count)) {
          $arg = $item->play_count;
        } else {
          $arg = 0;
        }
        $params = array(
          $arg,
          $link
        );
      break;
      case 'poll':
        $labelOne = 'suggest_%s vote | posted by %s';
        $labelMany = 'suggest_%s votes | posted by %s';
        $link = $this->view->htmlLink($owner->getHref(), $owner->getTitle(), array('id' => $num.'_suggest_'.$type.$id.'_owner_'.$owner->getGuid(), 'class' => 'he_tip_link'));
        if (isset($item->vote_count)) {
          $arg = $item->vote_count;
        } else {
          $arg = 0;
        }
        $params = array(
          $arg,
          $link
        );
      break;
      case 'question':
        $labelOne = 'suggest_%s view | posted by %s';
        $labelMany = 'suggest_%s views | posted by %s';
        $link = $this->view->htmlLink($owner->getHref(), $owner->getTitle(), array('id' => $num.'_suggest_'.$type.$id.'_owner_'.$owner->getGuid(), 'class' => 'he_tip_link'));
        if (isset($item->question_views)) {
          $arg = $item->question_views;
        } else {
          $arg = 0;
        }
        $params = array(
          $arg,
          $link
        );
      break;
      case 'quiz':
      case 'page':
      case 'pageblog':
      case 'pagealbum':
      case 'pagemusic':
      case 'pagediscussion_pagepost':
      case 'pagedocument':
      case 'avp_video':
      case 'ynmusic_album':
      case 'document':
      case 'list_listing':
      case 'artarticle':
      case 'blog':
      case 'classified':
      case 'video':
      case 'album':
      case 'album_photo':
      case 'article':
        $labelOne = 'suggest_%s view | posted by %s';
        $labelMany = 'suggest_%s views | posted by %s';
        $link = $this->view->htmlLink($owner->getHref(), $owner->getTitle(), array('id' => $num.'_suggest_'.$type.$id.'_owner_'.$owner->getGuid(), 'class' => 'he_tip_link'));
        if (isset($item->view_count)) {
          $arg = $item->view_count;
        } else {
          $arg = 0;
        }
        $params = array(
          $arg,
          $link
        );
      break;
    }

    return $html.$this->view->translate(array($labelOne, $labelMany, $arg), $params);
  }

}