<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Adapter_Embed extends Ynultimatevideo_Plugin_Adapter_Abstract {
	 public function compileVideo($params) 
	 {
	     $video_id = $params['video_id'];
         $code = $params['code'];
         $view = $params['view'];
         $videoEmbedded = "";
         $mobile = $params['mobile'];
         $autoplay = !$mobile && $view;
         if($code)
         {
            $videoFrame = $video_id."_".$params['count_video'];
            $videoEmbedded = '<iframe
                title="Embed video player"
                id="videoFrame' . $videoFrame . '"
                class="vimeo_iframe' . ($view ? "_big" : "_small") . '"' .
                            'src="'. $code .'"' . ($autoplay ? "&autoplay=1" : "") . '
                frameborder="0"
                allowfullscreen=""
                scrolling="no">
               </iframe>
              <script type="text/javascript">
              en4.core.runonce.add(function() {
                var doResize = function() {
                  var aspect = 16 / 9;
                  var el = document.id("videoFrame' . $videoFrame . '");
                  var parent = el.getParent();
                  var parentSize = parent.getSize();
                  el.set("width", parentSize.x);
                  el.set("height", parentSize.x / aspect);
                }
                window.addEvent("resize", doResize);
                doResize();
              });
            </script>';
         }
         return $videoEmbedded;
     }
     public function isValid() 
     {
        if (array_key_exists('link', $this->_params)) 
        {
            preg_match('/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/', $this->_params['link'], $matches);
            if(count($matches) > 2)
            {
                return true;
            }
        }
        return false;
    }


    public function extractVideo($params) {
        return $this->compileVideo($params);
    }

    public function getVideoLargeImage(){}
    public function getVideoDuration(){}
    public function getVideoTitle(){}
    public function getVideoDescription(){}
    public function fetchLink(){}
}