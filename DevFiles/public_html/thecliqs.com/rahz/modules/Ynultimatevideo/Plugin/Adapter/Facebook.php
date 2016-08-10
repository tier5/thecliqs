<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Adapter_Facebook extends Ynultimatevideo_Plugin_Adapter_Abstract {

    public function extractCode() {
        $link = $this->_params['link'];
        $regex = "/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/";
        preg_match($regex, $link, $matches);
        $code = $matches[2];
        return $code;
    }

    /**
     *
     * @return : false if the link is invalid, otherwise return an SimpleXMLElement object containing the video information
     */
    public function isValid() {
        if (array_key_exists('code', $this->_params)) {
            $code = $this->_params['code'];
        }
        if (empty($code) && array_key_exists('link', $this->_params)) {
            $code = $this->extractCode();
            $this->_params['code'] = $code;
        }
        if ($code) {
            $url = "https://www.facebook.com/video/embed?video_id=$code";
            if(@file_get_contents($url))
                return $code;
        }
        return false;
    }

    /**
     *
     * @return type
     */
    public function fetchLink() {
        $code = $this->isValid();
        if (!$code) {
            return false;
        } else {
            $this->_information = array();
            $this->_information['code'] = $code;
            $this->_information['large-thumbnail'] = "https://graph.facebook.com/$code/picture";
        }
        return true;
    }

    public function getVideoLargeImage() {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('large-thumbnail', $this->_information)) {
            return $this->_fetchImage($this->_information['large-thumbnail']);
        }
    }
    public function _fetchImage($photo_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $photo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $data = curl_exec($ch);
        curl_close($ch);
        $tmpfile = APPLICATION_PATH_TMP . DS . md5($photo_url) . '.jpg';
        @file_put_contents($tmpfile, $data);
        return $tmpfile;
    }

    public function getVideoDuration() {
        return "";
    }

    public function getVideoTitle() {
        return "";
    }

    public function getVideoDescription() {
        return "";
    }

    public function getEmbededCode($code = null) {

    }

    public function compileVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile'])?false:$params['mobile'];

        $autoplay = !$mobile && $view;
        $videoFrame = $video_id."_".$params['count_video'];
        $embedded = '
            <iframe
            title="Facebook video player"
            id="videoFrame' . $videoFrame . '"
            class=""'.'
            src="//www.facebook.com/video/embed?video_id=' . $code . '"
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
        return $embedded;
    }

    public function extractVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile'])?false:$params['mobile'];

        $embedded = '
            <video id="player_' . $video_id . '" class="ynultimatevideo-player" data-type="1" width="764" height="426">
                <source type="video/facebook" src="www.facebook.com/video/embed?video_id='. $code .'" />
            </video>';

        return $embedded;
    }
}
