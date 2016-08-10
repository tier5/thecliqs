<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Plugin_Core
{
  public function addActivity($event)
  {
    $payload = $event->getPayload();
  	$actionType = $payload['type'];
    $api = Engine_Api::_()->getApi('core', 'suggest');
    $actionTypesPairs = $api->getActionTypes();
    $actionTypes = array_values($actionTypesPairs);
    $session = new Zend_Session_Namespace();

    if (in_array($actionType, $actionTypes) || $actionType == 'quiz_take') {
      $object = $payload['object'];

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

      $select = $actionTable->select()
          ->where('type = ?', $actionType)
          ->where('object_type = ?', $object->getType())
          ->where('object_id = ?', $object->getIdentity())
          ->where('date > ?', date('Y-m-d H:i:s', time()-30));

      $action = $actionTable->fetchRow($select);

      $viewer = Engine_Api::_()->user()->getViewer();

      if (!$action || !$viewer->membership()->getMemberCount(true)){
        return ;
      }

      $session->suggest_type = $actionType;
      $session->object_type = $object->getType();
      $session->object_id = $object->getIdentity();
      $session->show_popup = (bool)$api->isAllowed($actionType);
    }
  }

  public function onItemDeleteAfter($event)
  {
    $payload = $event->getPayload();
    $type = $payload['type'];
    $id = $payload['identity'];

    $itemTypes = array_keys(Engine_Api::_()->suggest()->getItemTypes());
    if (in_array($type, $itemTypes)) {
      $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
      $suggests = $table->fetchAll($table->getSelect(array(
        'object_type' => $type,
        'object_id'   => $id
      )));

      foreach ($suggests as $suggest) {
        $suggest->delete();
      }
    }
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    $user_id = $payload['user_id'];
    if ($user_id) {
      $table = Engine_Api::_()->getDbTable('suggests', 'suggest');
      $select = $table
        ->select()
        ->where(new Zend_Db_Expr('object_type = "user" AND object_id = '.(int)$user_id))
        ->orWhere('from_id = ?', $user_id)
        ->orWhere('to_id = ?', $user_id);

      $suggests = $table->fetchAll($select);
      foreach ($suggests as $suggest) {
        $suggest->delete();
      }
    }
  }

  public function onRenderLayoutDefault($event)
  {
    $front = Zend_Controller_Front::getInstance();
    $view = $event->getPayload();
    $session = new Zend_Session_Namespace();

    if ($view instanceof Zend_View) {
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR
          ."modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";

      $options = array(
        'm' => 'suggest',
        'c' => 'HESuggest.suggest',
        'l' => 'getSuggestItems',
        'nli' => 0,
        'ipp' => 30
      );

      if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
        $script = <<<EOL

          var internalTips = null;
          var initLikeHintTips = function(elements){
            var options = {
              url: "{$view->url(array('action' => 'show-user'), 'like_default')}",
              delay: 300,
              onShow: function(tip, element) {
                var miniTipsOptions = {
                  'htmlElement': '.he-hint-text',
                  'delay': 1,
                  'className': 'he-tip-mini',
                  'id': 'he-mini-tool-tip-id',
                  'ajax': false,
                  'visibleOnHover': false
                };

                var internals = \$(tip).getElements('.he-hint-tip-links');
                internalTips = new HETips(internals, miniTipsOptions);
                Smoothbox.bind();
              }
            };
            var thumbs = [];
            if (!elements) {
              thumbs = \$\$('.he_tip_link');
            } else {
              thumbs = elements;
            }
            var mosts_hints = new HETips(thumbs, options);
          }

          en4.core.runonce.add(function(){
            initLikeHintTips();
          });
EOL;
        $view->headScript()->appendScript($script);
      }

      $subject = null;

      if (Engine_Api::_()->core()->hasSubject()) {
        if ($front->getRequest()->getParam('content') && $front->getRequest()->getParam('content_id')) {
          switch ($front->getRequest()->getParam('content')){
            case 'blog':
              $subject_type = 'pageblog';
              break;
            case 'discussion':
              $subject_type = 'pagediscussion_pagepost';
              break;
            case 'video':
              $subject_type = 'pagevideo';
              break;
            case 'page_event':
              $subject_type = 'pageevent';
              break;
            case 'document':
              $subject_type = 'pagedocument';
              break;
            case 'review':
              $subject_type = 'pagereview';
              break;
            default:
              $subject_type = $front->getRequest()->getParam('content');
              break;
            }

            $subject = Engine_Api::_()->core()->getSubject();
            $subject_id = (int)$front->getRequest()->getParam('content_id');
            $subject_title = Engine_Api::_()->getItem($subject_type, $subject_id)->getTitle();
        }
        else {
            $subject = Engine_Api::_()->core()->getSubject();
            $subject_type = $subject->getType();
            $subject_id = $subject->getIdentity();
            $subject_title = $subject->getTitle();
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $type = str_replace('_', '.', $subject_type);
        $type = ($type == 'album.photo') ? 'photo' : $type;
        $options['t'] = $view->translate('suggest_popup_title_'.$subject_type.' %s', $subject_title);
        if ($settings->getSetting('suggest.link.'.$type)) {
          $options['params'] = array(
            'suggest_type' => 'link_'.$subject_type,
            'object_type' => $subject_type,
            'object_id' => (int)$subject_id,
            'scriptpath' => $path,
            'potential' => (int)($subject_type == 'user')
          );
          $script = $view->partial('popup/init.tpl', 'suggest', array(
            'options' => $options
          ));

          $view->headScript()->appendScript($script);
        }
      }

      $list_modules = array('ynmusic', 'avp', 'advancedarticles', 'list', 'document', 'job');
      $module = $front->getRequest()->getModuleName();
      $action = $front->getRequest()->getActionName();
      $controller = $front->getRequest()->getControllerName();
      $validItem = $front->getRequest()->getModuleName() != 'suggest';
      $viewer = Engine_Api::_()->user()->getViewer();

      if ($subject && $validItem && ($action == 'view' || ($controller == 'profile' && $action == 'index') || ($controller == 'product' && $action == 'index') || in_array($module, $list_modules)) && !$viewer->isSelf($subject) ) {
        $this->appendShareBox();
      }

      $view->headScript()
        ->appendFile('application/modules/Suggest/externals/scripts/core.js');

      if (isset($session->show_popup) && $session->show_popup) {
        $object = Engine_Api::_()->getItem($session->object_type, $session->object_id);

        if ($object) {
          $options['t'] = $view->translate('suggest_popup_title_'.$object->getType().' %s', $object->getTitle());
          $options['params'] = array(
            'suggest_type' => $session->suggest_type,
            'object_type' => $session->object_type,
            'object_id' => (int)$session->object_id,
            'scriptpath' => $path,
            'potential' => (int)($session->object_type == 'user')
          );

          if ($session->suggest_type == 'fr_sent' || $session->suggest_type == 'fr_confirm') {
            $options['timeout'] = 1000;
          } else {
            $options['timeout'] = 1000;
          }

          $script = $view->partial('popup/js.tpl', 'suggest', array(
            'options' => $options
          ));

          $view->headScript()->appendScript($script);
        }
        Engine_Api::_()->suggest()->clearSession();
      }
    }
  }

  protected function appendShareBox()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $app_id = $settings->getSetting('suggest.facebook.app.id', false);

    if ($app_id) {
      $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

      try {
        $og_app_id = $app_id;
        $og_title = $subject->getTitle() ? $subject->getTitle() : false;
        $og_type = 'website';
        $og_url = $subject->getHref() ? $host_url . $subject->getHref() : false;
        $og_image = $subject->getPhotoUrl() ? $host_url . $subject->getPhotoUrl() : false;
      } catch (Exception $e) {
      }

      echo ($og_app_id) ? '<meta property="fb:app_id" content="' . $og_app_id . '"/>' . "\n" : '';
      echo ($og_title) ? '<meta property="og:title" content="' . $og_title . '"/>' . "\n" : '';
      echo ($og_type) ? '<meta property="og:type" content="' . $og_type . '"/>' . "\n" : '';
      echo ($og_url) ? '<meta property="og:url" content="' . $og_url . '"/>' . "\n" : '';
      echo ($og_image) ? '<meta property="og:image" content="' . $og_image . '"/>' . "\n" : '';
    }
    $share = $view->partial('share/box.tpl', 'suggest', array('subject' => $subject, 'app_id' => $app_id));
    $view->headScript()->appendScript($share);
  }
}