<?php

class Suggest_View_Helper_SuggestItem extends Zend_View_Helper_Abstract
{
  public function suggestItem($item)
  {

   try{
     $options = array(
       't' => $this->view->translate('suggest_popup_title_'.$item->getType().' %s', $item->getTitle()),
       'c' => 'HESuggest.suggest',
       'l' => 'getSuggestItems',
       'm' => 'suggest',
       'params' => array(
         'suggest_type' => 'link_'.$item->getType(),
         'object_type' => $item->getType(),
         'object_id' => (int)$item->getIdentity(),
         'potential' => (int)($item->getType() == 'user')
       )
     );

    $url = $this->view->url(array(
      'controller' => 'index',
      'action' => 'suggest',
      'object_id' => $options['params']['object_id'],
      'object_type' => $options['params']['object_type'],
      'suggest_type' => 'link_'.$options['params']['object_type']
    ), 'suggest_general');

    $options = Zend_Json_Encoder::encode($options);

    $html = $this->view->htmlLink('javascript:void(0)', $this->view->translate('Suggest To Friends'), array(
      'class' => 'buttonlink',
      'onclick' => 'HESuggest.suggestItem("'.$url.'", '.$options.')',
      'style' => 'background: no-repeat scroll left top transparent url(application/modules/Suggest/externals/images/suggest.png)'
    ));

    return $html;
   }catch (Exception $e){return false;}
  }
}