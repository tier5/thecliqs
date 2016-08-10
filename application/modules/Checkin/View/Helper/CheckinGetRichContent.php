<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CheckinGetRichContent.php 30.11.11 19:36 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_View_Helper_CheckinGetRichContent extends Zend_View_Helper_Abstract
{
  public function checkinGetRichContent($view = false, $params = array(), $item)
  {
    if ($item instanceof Video_Model_Video) {
      $session = new Zend_Session_Namespace('mobile');
      $mobile = $session->mobile;
      $action_id = $params['action_id'];

      // if video type is youtube
      if ($item->type == 1){
        $videoEmbedded = $item->compileYouTube($item->video_id, $item->code, $view, $mobile);
      }
      // if video type is vimeo
      if ($item->type == 2){
        $videoEmbedded = $item->compileVimeo($item->video_id, $item->code, $view, $mobile);
      }

      // if video type is uploaded
      if ($item->type ==3){
        $video_location = Engine_Api::_()->storage()->get($item->file_id, $item->getType())->getHref();
        $videoEmbedded = $item->compileFlowPlayer($video_location, $view);
      }

      // $view == false means that this rich content is requested from the activity feed
      if($view == false) {
        // prepare the duration
        //
        $video_duration = "";
        if( $item->duration ) {
          if( $item->duration >= 3600 ) {
            $duration = gmdate("H:i:s", $item->duration);
          } else {
            $duration = gmdate("i:s", $item->duration);
          }
          $duration = ltrim($duration, '0:');

          $video_duration = "<span class='video_length'>".$duration."</span>";
        }

        // prepare the thumbnail
        $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, 'thumb.video.activity');

        if( $item->photo_id ) {
          $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, 'thumb.video.activity');
        } else {
          $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png">';
        }

        if( !$mobile ) {
          $thumb = '<a id="video_thumb_'.$item->video_id.'" style="" href="javascript:void(0);" onclick="javascript: $(\'action_'.$action_id.'\').setStyle(\'float\', \'none\'); var myElement = $(this);myElement.style.display=\'none\';var next = myElement.getNext(); next.style.display=\'block\';">
                    <div class="video_thumb_wrapper">'.$video_duration.$thumb.'</div>
                    </a>';
        } else {
          $thumb = '<a id="video_thumb_'.$item->video_id.'" class="video_thumb" href="javascript:void(0);" onclick="javascript: $(\'videoFrame'.$item->video_id.'\').style.display=\'block\'; $(\'videoFrame'.$item->video_id.'\').src = $(\'videoFrame'.$item->video_id.'\').src; var myElement = $(this); myElement.style.display=\'none\'; var next = myElement.getNext(); next.style.display=\'block\';">
                    <div class="video_thumb_wrapper">'.$video_duration.$thumb.'</div>
                    </a>';
        }

        // prepare title and description
        $title = "<a href='".$item->getHref($params)."'>$item->title</a>";
        $videoEmbedded = $thumb.'<div id="video_object_'.$item->video_id.'" class="video_object">'.$videoEmbedded.'</div><div class="video_info">'.$title.'</div>';
      }

      return $videoEmbedded;
    }
    elseif ($item instanceof Music_Model_PlaylistSong) {
      $playlist      = $item->getParent();
      $videoEmbedded = '';

      // $view == false means that this rich content is requested from the activity feed
      if( $view == false ) {
        $desc   = strip_tags($playlist->description);
        $desc   = "<div class='music_desc'>".(Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc)."</div>";
        $zview  = Zend_Registry::get('Zend_View');
        $zview->playlist     = $playlist;
        $zview->songs        = array($item);
        $zview->short_player = true;
        $videoEmbedded       = $desc . $zview->render('application/modules/Checkin/views/scripts/_Player.tpl');
      }

      return $videoEmbedded;
    }
  }
}