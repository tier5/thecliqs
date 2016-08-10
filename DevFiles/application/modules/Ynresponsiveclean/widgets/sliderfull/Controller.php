<?php

class Ynresponsiveclean_Widget_SliderfullController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $content_type = $this -> _getParam('content_type', 'ynresponsiveclean_latest_events');
    list($module, $type) = explode('_', $content_type, 2);

    if (!Engine_Api::_() -> hasModuleBootstrap($module))
    {
      return $this -> setNoRender(true);
    }

    $api = Engine_Api::_() -> {$module}();

    if (!method_exists($api, 'getSliderContent'))
    {
      return $this -> setNoRender(true);
    }
    $items = $api -> getSliderContent($type, $this -> _getAllParams());
    if (!count($items))
    {
      return $this -> setNoRender(true);
    }
    $slider_type = $this -> _getParam('slider_type', 'parallax');
    $show_title = $this -> _getParam('show_title', false);

    $show_description = $this -> _getParam('show_description', false);    
    
	$height =  $this->_getParam('height', 450);	    
    if (!$height) {
        $height = 450;
    }
	
	$image_height = $this->_getParam('image_height', 100);    
    if (!$image_height) {
        $image_height = 100;
    }
    
    $background_image = $this -> _getParam('background_image', null);

    if (!$background_image)
    {
      $background_image = $this->view->layout()->staticUrl . 'application/themes/ynresponsive1/images/slideshow_bg.jpg';
    }
    
    $this->view->height = $height;
	$this->view->image_height = $image_height;

    $this -> view -> html_content = $this -> view -> partial('_' . $slider_type . 'Slider.tpl', 'ynresponsive1', array(
      'items' => $items,
      'show_title'=> $show_title,
      'show_description'=>$show_description,
      'background_image'=>$background_image,
	  'height'=>$height,
	  'image_height'=>$image_height,
      'slider_id' => '_' . uniqid()
    ));
    
  }
}
