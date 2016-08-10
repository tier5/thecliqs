<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */

defined('YOUTUBE_API_URL') or define('YOUTUBE_API_URL', "https://www.googleapis.com/youtube/v3/");

class Ynvideochannel_Api_Core extends Core_Api_Abstract
{
    /**
     * @return mixed
     */
    protected function getApiKey()
    {
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.apikey', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M');
    }

    /**
     * @param $url
     * @return bool
     */
    public function checkChannelUrlValid($url)
    {
        $url = trim($url);
        $pattern = "/((http|https):\/\/|)(www\.|)youtube\.com\/(channel\/|user\/)[a-zA-Z0-9]{1,}/";
        if (preg_match($pattern, $url)) {
            return true;
        }
        return false;
    }

    /**
     * @param $channelId
     * @return string
     */
    public function getChannelInfoUrl($channelId)
    {
        return YOUTUBE_API_URL."channels?part=brandingSettings,snippet&id=$channelId&key=" . $this->getApiKey();
    }

    /**
     * @param $forUsername
     * @return string
     */
    public function getChannelUserUrl($forUsername)
    {
        return YOUTUBE_API_URL."channels?part=brandingSettings,snippet&forUsername=$forUsername&key=" . $this->getApiKey();
    }

    /**
     * @param $channelId
     * @return string
     */
    public  function getChannelVideosUrl($channelId)
    {
        return YOUTUBE_API_URL."search?key=" . $this->getApiKey() . "&channelId=$channelId&part=snippet&order=date";
    }

    /**
     * @param $sQuery
     * @param $sPageToken
     * @param $iMaxResult
     * @return string
     */
    public  function getFindChannelUrl($sQuery, $sPageToken, $iMaxResult)
    {
        return YOUTUBE_API_URL.'search?part=snippet&type=channel&q='.$sQuery.'&key='.$this->getApiKey() . '&pageToken='.$sPageToken.'&maxResults='.$iMaxResult;
    }

    /**
     * @param $channelCode
     * @param $userId
     * @return object|null
     */
    public function isExistChannelCode($channelCode, $userId)
    {
        //TODO check channel exists from db, if exists => return channel object
        $channelTable = Engine_Api::_() -> getDbTable('channels', 'ynvideochannel');
        $select = $channelTable->select()->where('channel_code = ?', $channelCode)->where('owner_id =?', $userId)->limit(1);
        return $channelTable -> fetchRow($select)? $channelTable -> fetchRow($select):null;
    }

    /**
     * @param $videoCode
     * @param null $channelId
     * @param null $ownerId
     * @return bool
     */
    public function isExistVideoCode($videoCode, $channelId = NULL, $ownerId = NULL)
    {
        $videoTable = Engine_Api::_() -> getDbTable('videos', 'ynvideochannel');
        if($channelId){
            $select = $videoTable->select()->where('code = ?', $videoCode)->where('channel_id =?', $channelId)->limit(1);
            return $videoTable -> fetchRow($select)?true:false;
        }
        if($ownerId){
            $select = $videoTable->select()->where('code = ?', $videoCode)->where('owner_id =?', $ownerId)->limit(1);
            return $videoTable -> fetchRow($select)?true:false;
        }
            return false;
    }

    /**
     * @param $channelFeedUrl
     * @param int $maxNum
     * @param int $iTotalVideosOfChannel
     * @param null $channel_id
     * @return array
     */
    public function getVideosFromChannelUrl($channelFeedUrl, $maxNum = 10, $iTotalVideosOfChannel = 0, $channel_id = null)
    {
        $videoCnt = 0;
        $outVideos = array();
        // iterate 1000 times gonna be enough
        $iThreshold = 1000;
        $iPage = 1;
        $iMaxResultForEachQuery = 40;
        $earlyBreak = false;
        $sNextPageToken = '';

        // videoCnt is number of video we got
        // maxNum is number of video we want to get

        while ($videoCnt < $maxNum) {
            $sUrl = $channelFeedUrl . '&pageToken=' . $sNextPageToken . '&maxResults=' . $iMaxResultForEachQuery.'&type=video';
            $oChannel = @file_get_contents($sUrl);
            $oChannel = json_decode($oChannel);
            if (!$oChannel || !isset($oChannel->items))
                break;

            foreach ($oChannel->items as $oVideo) {
                if (!$oVideo)
                    continue;
                if ($channel_id && $this->isExistVideoCode($oVideo->id->videoId, $channel_id, null))
                    continue;

                $outVideo['video_id'] = $oVideo->id->videoId;
                $outVideo['title'] = $oVideo->snippet->title;
                $outVideo['url'] = 'https://www.youtube.com/watch?v=' . $oVideo->id->videoId;
                $outVideo['duration'] = 0;
                if (isset($oVideo->snippet->description)) {
                    $outVideo['description'] = $oVideo->snippet->description;
                } else {
                    $outVideo['description'] = '';
                }
                $outVideo['time_stamp'] = isset($oVideo->snippet->publishedAt) ? strtotime($oVideo->snippet->publishedAt) : 0;
                $thumbnails = end($oVideo->snippet->thumbnails);
                $outVideo['image_path'] = sprintf("%s", $thumbnails->url);
                $outVideos[] = $outVideo;
                $videoCnt++;
                if ($videoCnt >= $maxNum) {
                    // break in the middle of a page in case we get enough data
                    $earlyBreak = true;
                    break;
                }
            }

            // get page token for next api call
            $sNextPageToken = isset($oChannel->nextPageToken) ? $oChannel->nextPageToken : '';
            // Limit the number of page and check if we reach the last page
            if ($earlyBreak || $iPage >= $iThreshold || !$sNextPageToken) {
                break;
            }
        }
        return $outVideos;
    }

    /**
     * @param $channelFeedUrl
     * @param $pageTokenPrev
     * @param $pageTokenNext
     * @return array
     */
    public function getChannels($channelFeedUrl, $userId)
    {
        $data = @file_get_contents($channelFeedUrl);
        $data = json_decode($data);
        $items = $data->items;
        $pageTokenPrev = $pageTokenNext = "";

        $aChannels = array(); //Result Array
        $aExist = array();    //Array for exist channels
        if ($data) {
            if (isset($data->prevPageToken))
                $pageTokenPrev = $data->prevPageToken;
            if (isset($data->nextPageToken))
                $pageTokenNext = $data->nextPageToken;
        }
        $channelIds = array();
        foreach ($items as $entry)
        {
            $channelIds[] = $entry->snippet->channelId;
        }

        $iMaxResult = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.channels', 50);
        $channelDetailUrl = Engine_Api::_()->ynvideochannel()->getChannelDetailUrl($channelIds, $iMaxResult);
        $channelInfo = @file_get_contents($channelDetailUrl);
        $channelInfo = json_decode($channelInfo);
        $items = $channelInfo -> items;
        foreach ($items as $entry) {
            if ($entry->snippet->title != "") {
                $channel = array();
                $channel['channel_id'] = $entry->id;
                $channel['link'] = 'https://www.youtube.com/channel/' . $entry->id;
                $channel['title'] = strip_tags($entry->snippet->title);
                $channel['url'] = $this -> getChannelVideosUrl($entry->id);
                $channel['summary'] = $entry->snippet->description;
                //Check if channel is exist
                $existChannel = $this->isExistChannelCode($entry->id, $userId);
                if ($existChannel) {
                    $channel['isExist'] = $existChannel->getIdentity();
//                    $channel['link'] = $existChannel->getHref(); //show chanel detail when use want check it existed.
//                    $channel['summary'] = $existChannel -> description;
                }
                $thumbnails = end($entry->snippet->thumbnails);
                $channel['video_image'] = sprintf("%s", $thumbnails->url);
                $channel['subscriber_count'] = $entry->statistics -> subscriberCount;
                $channel['video_count'] = $entry->statistics -> videoCount;

                if ($existChannel)
                    $aExist[] = $channel;
                else
                    $aChannels[] = $channel;

            }
        }
        $aChannels = array_reverse($aChannels);
        $aChannels = array_merge($aExist, $aChannels);

        return array($aChannels, $pageTokenPrev, $pageTokenNext);
    }

    /**
     * @param $code
     * @return array|null
     */
    public function fetchVideoLink($code)
    {
        $api_key = $this -> getApiKey();
        $url = YOUTUBE_API_URL."videos?id=$code&key=$api_key&part=snippet,contentDetails";
        $data = @file_get_contents($url);
        $data = json_decode($data);
        if (empty($data->items)) {
            return null;
        } else {
            $data = $data->items[0];
        }
        $information = null;
        if($data) {
            $information = array();
            $information['title'] = sprintf("%s", $data->snippet->title);
            $start = new DateTime('@0'); // Unix epoch
            $start->add(new DateInterval($data->contentDetails->duration));
            $duration = $start->format('H') * 60 * 60 + $start->format('i') * 60 + $start->format('s');
            $information['duration'] = sprintf("%s", $duration);
            $information['description'] = sprintf("%s", $data->snippet->description);
            $thumbnails = $data->snippet->thumbnails;
            $thumbnail = $thumbnails->high;
            $information['large-thumbnail'] = sprintf("%s", $thumbnail->url);
            if(isset($thumbnails -> medium))
                $thumbnail = $thumbnails -> medium;
            $information['medium-thumbnail'] = sprintf("%s", $thumbnail->url);
        }
        return $information;
    }
    public function typeCreate($label)
    {
        $field = Engine_Api::_() -> fields() -> getField('1', 'ynvideochannel_video');
        // Create new blank option
        $option = Engine_Api::_() -> fields() -> createOption('ynvideochannel_video', $field, array(
            'field_id' => $field -> field_id,
            'label' => $label,
        ));
        // Get data
        $mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynvideochannel_video');
        $metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynvideochannel_video');
        $optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynvideochannel_video');
        // Flush cache
        $mapData -> getTable() -> flushCache();
        $metaData -> getTable() -> flushCache();
        $optionData -> getTable() -> flushCache();

        return $option -> option_id;
    }

    public function getCategory($category_id)
    {
        return Engine_Api::_() -> getDbtable('categories', 'ynvideochannel') -> find($category_id) -> current();
    }

    public function getChannelDetailUrl($channelId, $iMaxResult)
    {
        $Ids = implode(',',$channelId);
        return YOUTUBE_API_URL.'channels?id='.$Ids.'&part=snippet,statistics&key='.$this->getApiKey().'&maxResults='.$iMaxResult;
    }

    public function isValidVideo($videoCode)
    {
        $video_url = @file_get_contents('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v='.$videoCode);
        if($video_url) {
            return true;
        }
            return false;
    }
}

