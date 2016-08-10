<?php
  $type = $this->object->getType();
  $id = $this->object->getIdentity();
  $label = $this->translate("suggest_view_this_".$type);

  switch ($type) {
    case 'group':
    case 'event':
      $url = $this->url(array(
          'controller' => 'member',
          'action' => 'join',
          $type.'_id' => $id
        ), $type.'_extended');

      $params = array('class' => 'smoothbox buttonlink icon_'.$type.'_join suggest_widget_link');
    break;
    case 'user':
      $url = $this->url(array(
          'controller' => 'friends',
          'action' => 'add',
            'user_id' => $id
        ), 'user_extended');

      $params = array('class' => 'smoothbox buttonlink icon_friend_add suggest_widget_link suggest-user-item');
    break;
    case 'page':
      $url = 'javascript:void(0)';
      if(Engine_Api::_()->like()->isLike($this->object, Engine_Api::_()->user()->getViewer())){
        $params = array('class' => 'suggest_page_unlike suggest_widget_link page_buttonlink', 'id' => $id);
        $label = $this->translate('SUGGEST_suggest_page_unlike');
      }
      else {
        $params = array('class' => 'suggest_page_like suggest_widget_link  page_buttonlink', 'id' => $id);
        $label = $this->translate('SUGGEST_suggest_page_like');
      }
    break;
    default:
      $url = $this->url(array(
          'controller' => 'index',
          'action' => 'accept-suggest',
          'object_type' => $type,
          'object_id' => $id,
        ), 'suggest_general');

      $params = array('class' => 'buttonlink suggest_widget_link suggest_view_'.$type, 'target' => '_blank');
    break;
  }
  
  echo $this->htmlLink($url, $label, $params);
?>