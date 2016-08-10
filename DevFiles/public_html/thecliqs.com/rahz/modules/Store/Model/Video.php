<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Video.php 2011-09-07 17:18:11 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Video extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'store_product';

  protected $_owner_type = 'user';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'store_profile',
      'product_id' => $this->product_id,
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getRichContent($view = false, $params = array())
  {
    // if video type is youtube
    if ($this->type == 1){
      $videoEmbedded = $this->compileYouTube($this->code, $view);
    }
    // if video type is vimeo
    if ($this->type == 2){
      $videoEmbedded = $this->compileVimeo($this->code, $view);
    }

    return $videoEmbedded;
  }

  public function compileYouTube($code, $view)
  {
    //560 x 340
    $embedded = '
    <object width="'.($view?"560":"425").'" height="'.($view?"340":"344").'">
    <param name="movie" value="http://www.youtube.com/v/'.$code.'&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
    <param name="allowFullScreen" value="true"/>
    <param name="allowScriptAccess" value="always"/>
    <param name="wmode" value="transparent" />
    <embed wmode="transparent" src="http://www.youtube.com/v/'.$code.'&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1'.($view?"":"&autoplay=1").'" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="'.($view?"560":"425").'" height="'.($view?"340":"344").'"/>
    </object>';

    return $embedded;
  }

  public function compileVimeo($code, $view)
  {
    //640 x 360
    $embedded = '
    <object width="'.($view?"640":"400").'" height="'.($view?"360":"230").'">
    <param name="allowFullScreen" value="true" />
    <param name="allowScriptAccess" value="always" />
    <param name="wmode" value="transparent" />
    <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$code.'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" />
    <embed wmode="transparent" src="http://vimeo.com/moogaloop.swf?clip_id='.$code.'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1'.($view?"":"&autoplay=1").'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.($view?"640":"400").'" height="'.($view?"360":"230").'"/>
    </object>';

    return $embedded;
  }
}