<?php

class Ynmobile_Helper_Video extends Ynmobile_Helper_Base{
    
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('video','ynmobile'); 
    }
    
    
    function field_id(){
        $this->data['iVideoId'] =  $this->entry->getIdentity();
    }
    
    function field_as_attachment(){
        parent::field_as_attachment();
        $this->field_desc();
        $this->field_origin();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_href();
    }
    
    
    function field_category(){
        $this->data['sCategory'] = $this->entry->category_id;
        $this->data['iCategoryId'] =  (int)$this->entry->category_id;    
    }
    
    function field_status(){
        // 0: is in proccessing or failed, 1: completed.
        $this->data['bInProcess'] = ($this->entry -> status) ? 0 : 1;
    }
    
    function field_duration(){
        $this->data['iDuration'] = (int)$this->entry -> duration;
    }
    
    function field_origin(){
        switch($this->entry->type){
            case 1:
                $this->data['sOriginalHostName'] =  'www.youtube.com';
                break;
            case 2:
                $this->data['sOriginalHostName'] = 'www.vimeo.com';
                break;
            default:
        }
    }
    
    function field_imgFull(){
        
        $video = $this->entry;
        
        $code = $video->code;
        
        $sImage =  '';
        
        if($video -> type == 1)
        {
            $sImage = "http://img.youtube.com/vi/$code/hqdefault.jpg";
        }
        else if($video -> type == 2)
        {
            $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
            $sImage = $data -> video -> thumbnail_large;
            $sImage = sprintf("%s",$sProfileImage);
        }
        
        if($sImage){
            $this->data['sFullPhotoUrl'] =  $sImage;
        }else{
            $this->_field_img('','sFullPhotoUrl');
        }
    }
    function field_info(){
        $video = $this->entry;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $owner = $video -> getOwner();
       
        $create = strtotime($video -> creation_date);
        // Prepare data in locale timezone
        $timezone = null;
        if (Zend_Registry::isRegistered('timezone'))
        {
            $timezone = Zend_Registry::get('timezone');
        }
        
        if (null !== $timezone)
        {
            $prevTimezone = date_default_timezone_get();
            date_default_timezone_set($timezone);
        }

        $sTime = date("D, j M Y G:i:s O", $create);

        if (null !== $timezone)
        {
            date_default_timezone_set($prevTimezone);
        }
        
        $info = array(
            'can_embed'=> true,
        );

        $can_embed =  true;
        
        if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('video.embeds', 1)){
            $can_embed =  false;
        }
        else
        if (isset($video -> allow_embed) && !$video -> allow_embed)
        {
            $can_embed =  false;
        }
        
        $embedCode = "";
        if ($can_embed)
        {
            // Get embed code
            $embedCode = $video -> getEmbedCode();
        }
        // increment count
        $embedded = "";
        if ($video -> status == 1)
        {
            if (!$video -> isOwner($viewer))
            {
                $video -> view_count++;
                $video -> save();
            }
            $embedded = $this -> getRichContent($video, true);
        }
        $video_location = "";
        if ($video -> type == 3 && //uploaded video 
            $video -> status == 1 && //converted or not
            $video->file1_id) // converted by mobile or not
        {
            if (!empty($video -> file_id))
            {
                //GETING H264 video
                $storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
                if ($storage_file)
                {
                    $video_location = $storage_file -> map();
                    $video_location = Engine_Api::_() -> ynmobile() ->finalizeUrl($video_location);
                }
            }
            
        }
        
        $types =  $this->getVideoTypes();
		
        $info = array(
            'bInProcess' => $this->entry->status?0:1,
            'sType' => $types[$video->type],
            'sCode' => $video -> code,
            'iDuration' => $video -> duration,
            'iTotalView' => $video -> view_count,
            'itimestamp' => $create,
            'sTimeStamp' => $sTime,
            'sEmbedCode' => $embedCode,
            'sVideoUrl' => $video_location,
            'sEmbed' => $embedded,
        );
        
        $this->data =  array_merge($this->data, $info);
    }
        
    
    private function getRichContent($video, $view = false)
    {
        // if video type is youtube
        if ($video -> type == 1)
        {
            $videoEmbedded = '
                <iframe
                 title="YouTube video player"
                id="videoFrame' . $video -> video_id . '"
                class="youtube_iframe_big"' . 'width="640"
                height="360"
                src="http://www.youtube.com/embed/' . $video -> code . '?showinfo=0&wmode=opaque"
                frameborder="0"
                allowfullscreen=""
                scrolling="no">
                </iframe>';

        }
        // if video type is vimeo
        if ($video -> type == 2)
        {
            $videoEmbedded = '<iframe
                title="Vimeo video player"
                id="videoFrame' . $video -> video_id . '"
                class="vimeo_iframe_big"' . 'width="640"
                height="360"
                src="http://player.vimeo.com/video/' . $video -> code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque&amp;badge=0"
                frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" scrolling="no">
                </iframe>';
        }

        // if video type is uploaded
        if ($video -> type == 3)
        {
            $videoEmbedded = "";
        }
        return $videoEmbedded;
    }

    public function getVideoTypes()
    {
        return array(
            '1' => 'youtube',
            '2' => 'vimeo',
            '3' => 'uploaded',
            '4' => 'dailymotion'    
        );
    }

    function field_rate(){
            
        
        $iEntryId =  $this->entry->getIdentity();
        $iViewerId =  $this->getViewerId();
        
        /**
         * Ynvideo_Api_Core or Video_Api_Core
         */
        $coreApi  =  $this->getWorkingApi('core', 'video');
        
        $ratingCount =   $coreApi-> ratingCount($iEntryId);
        
        $rated = 0;
        
        if($iViewerId){
            $rated = $coreApi -> checkRated($iEntryId, $iViewerId) ? 1:0;    
        }
        
        $this->data['fRating'] = $this->entry->rating;
        $this->data['iRatingCount'] = $ratingCount;
        $this->data['bIsRating'] =  $rated;
        
    }
    
    function field_canRate(){
        $this->data['bCanRate'] =  Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'view');
    }
    


    
    function field_listing(){
        $this->field_id();
        $this->field_title();
        $this->field_type();
        $this->field_desc();
        $this->field_stats();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_user();
        $this->field_likes();
        $this->field_rate();
        $this->field_canRate();
        $this->field_origin();
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_info();
		$this->field_auth();
    }

}
