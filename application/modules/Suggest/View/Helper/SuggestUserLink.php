<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SuggestUserLink.php 2010-07-02 19:54 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_View_Helper_SuggestUserLink extends Zend_View_Helper_Abstract
{
  public function suggestUserLink($user, $viewer = null)
  {
    if( null === $viewer ) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!($user instanceof Core_Model_Item_Abstract)) {
      return '';
    }

    if( !$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) ) {
      return '';
    }

    // Get data
    $row = $viewer->membership()->getRow($user);
    if (!$row || !$row->active) {
      return '';
    }

    $item_id = $user->getIdentity();

    $url = 'javascript:void(0)';
    $label = $this->view->translate('Suggest To Friends');
    $params = array(
      'class' => 'buttonlink suggest_widget_link suggest_view_friend',
      'onClick' => 'window.friends.friend_' . $item_id . '.box();' );

    $suggestLink =  $this->view->htmlLink($url, $label, $params);

    $title = $this->view->translate("Suggest %s to your friends", $user->getTitle());
    
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules"
      . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";
    $path_js = Zend_Json_Encoder::encode($path);

    $suggestScript = <<<OUTPUT_JS
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      if (!window.friends) {
        window.friends = {};
      }

      var options = {
        c: "window.friends.callback_{$item_id}.suggest",
        listType: "all",
        m: "suggest",
        l: "getSuggestItems",
        t: "{$title}",
        ipp: 30,
        nli: 0,
        params: {
          scriptpath: {$path_js},
          suggest_type: 'link_user',
          object_type: 'user',
          object_id: {$item_id}
        }
      };

      window.friends.callback_{$item_id} = new FriendSuggest({$item_id});
      window.friends.friend_{$item_id} = new HEContacts(options);
    });
  </script>
OUTPUT_JS;

    $content = $suggestScript . $suggestLink;

    return $content;
  }
}