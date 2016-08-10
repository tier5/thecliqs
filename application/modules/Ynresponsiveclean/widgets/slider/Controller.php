<?php

class Ynresponsiveclean_Widget_SliderController extends Engine_Content_Widget_Abstract
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
    
	if($slider_type == 'parallax')
	{
	   $height =  $this->_getParam('height', null);	
       if (!$height) { $height = 540; }
	}
	else
	{
		$height =  $this->_getParam('height', null);
        if (!$height) { $height = 300; }
	}
    $this->view->height = $height;
    
    $background_image = $this -> _getParam('background_image', null);

    if (!$background_image)
    {
      $background_image = $this->view->layout()->staticUrl . 'application/themes/ynresponsive1/images/slideshow_bg.jpg';
    }

    $this -> view -> html_content = $this -> view -> partial('_' . $slider_type . 'Slider.tpl', 'ynresponsive1', array(
      'items' => $items,
      'show_title'=> $show_title,
      'show_description'=>$show_description,
      'background_image'=>$background_image,
	  'height'=>$height,
      'slider_id' => '_' . uniqid()
    ));
  }

}
