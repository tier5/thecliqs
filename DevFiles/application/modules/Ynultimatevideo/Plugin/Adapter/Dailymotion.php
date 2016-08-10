<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Adapter_Dailymotion extends Ynultimatevideo_Plugin_Adapter_Abstract {

    public function extractCode() {
        $link = $this->_params['link'];
        $params = @pathinfo($this->_params['link']);
        $arr = explode('_', $params['basename']);
        $code = $arr[0];

        return $code;
    }

    /**
     *
     * @return : false if the link is invalid, otherwise return an SimpleXMLElement object containing the video information 
     */
    public function isValid() {
        if ($this->_params['link'] || $this->_params['code']) {
            if (!array_key_exists('code', $this->_params) || !$this->_params['code']) {
                $code = $this->extractCode();
                $this->_params['code'] = $code;
            } else {
                $code = $this->_params['code'];
            }

            $url = "https://api.dailymotion.com/video/$code&fields=description,duration,embed_html,embed_url,id,thumbnail_large_url,thumbnail_medium_url,title";
            return $this->fetchURL($url);
        }
        return false;
    }

    /**
     *
     * @return type 
     */
    public function fetchLink() {
        $data = $this->isValid();
        if ($data === false) {
            return false;
        } else {
            $this->_information = array();
            $properties = get_object_vars($data);
            foreach ($properties as $key => $value) {
                $this->_information[$key] = $value;
            }
        }
        return true;
    }

    public function getVideoLargeImage() {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('thumbnail_large_url', $this->_information)) {
            return $this->_information['thumbnail_large_url'];
        }
    }

    public function getVideoDuration() {
        if (isset($this->_information['duration'])) {
            return $this->_information['duration'];    
        }
        return 0;
    }

    public function getVideoTitle() {
        return $this->title;
    }
    
    public function getVideoDescription() {
        return $this->description;
    }

    public function getEmbededCode() {
        $this->fetchLink();
        return $this->_information['embed_html'];
    }

    public function fetchURL($url) {
        ## HTTPS url that you are targeting.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.23 (Windows NT 5.1; U; en)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        ## Below two option will enable the HTTPS option.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $data = json_decode(curl_exec($ch));
        if (!isset($data->error)) {
            return $data;
        }
    }

    public function compileVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile']) ? false : $params['mobile'];
        
        if (!isset($code)) {
            $code = $this->_params['code'];
        }
        if (!isset($code)) {
            $code = $this->extractCode($this->_params['link']);
        }
        
        $autoplay = !$mobile && $view;
        $videoFrame = $video_id."_".$params['count_video'];
        if ($code) 
        {
            $embedded = '
                <iframe
                title="Dailymotion video player"
                id="videoFrame' . $videoFrame . '"
                class="dailymotions_iframe' . ($view ? "_big" : "_small") . '"' .
                            'src="//www.dailymotion.com/embed/video/' . $code . '?api=true' . ($autoplay ? "&amp;autoplay=1" : "") . '"
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
                </script>
            ';
            return $embedded;
        } 
        throw new Exception("The code is not found" . var_dump($params));
    }

    public function extractVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];

        $embedded = '
            <div id="player_' . $video_id . '" data-type="4" data-code="' . $code . '" class="ynultimatevideo-player" style="display:none;">
            <div id="player_' . $video_id . '_iframe"></div>
            </div>
        ';
        return $embedded;
    }
}