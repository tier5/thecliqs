<?php

class Ynvideochannel_Plugin_Task_GetVideos extends Core_Plugin_Task_Abstract
{
    function execute()
    {
        // CHANNELS TABLE, GET CHANNELS WITH AUTO_UPDATE 1
        $channelTable = Engine_Api::_()->getDbTable('channels', 'ynvideochannel');
        $select = $channelTable->select()->where('auto_update = ?', 1);
        $channels = $channelTable->fetchAll($select);
        if (!count($channels))
            return;

        // NUMBER OF VIDEO TO AUTO UPDATE EACH TIME
        $numberOfVideos = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideochannel.update.videos', 10);

        // PRIVACY ARRAY
        $roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );

        $auth = Engine_Api::_()->authorization()->context;
        $db = Engine_Api::_()->getDbtable('videos', 'ynvideochannel')->getAdapter();

        foreach ($channels as $channel) {
            $channelVideosUrl = Engine_Api::_() -> ynvideochannel() -> getChannelVideosUrl($channel->channel_code);
            $aVideos = Engine_Api::_()->ynvideochannel()->getVideosFromChannelUrl($channelVideosUrl, $numberOfVideos, null, $channel->channel_id);

            // SKIP IF THERE ARE NO NEW VIDEOS FOUND
            if (!count($aVideos))
                continue;

            $owner = $channel->getOwner();
            // READ CHANNEL PRIVACY
            $viewMax = 0;
            $commentMax = 0;
            foreach ($roles as $role){
                if($auth->getAllowed($channel, $role , 'view') == 1)
                    $viewMax++;
            }
            foreach ($roles as $role){
                if($auth->getAllowed($channel, $role , 'comment') == 1)
                    $commentMax++;
            }

            $db->beginTransaction();

            $vCount = 0;
            foreach ($aVideos as $video) {
                $code = $video['video_id'];
                $videoInformation = Engine_Api::_()->ynvideochannel()->fetchVideoLink($code);
                if ($videoInformation) {
                    $videoInformation['code'] = $code;
                    $videoInformation['owner_type'] = 'user';
                    $videoInformation['owner_id'] = $owner->getIdentity();
                    $videoInformation['parent_type'] = 'user';
                    $videoInformation['parent_id'] = $owner->getIdentity();
                    $videoInformation['channel_id'] = $channel->getIdentity();
                    $videoInformation['category_id'] = $channel->category_id;

                    try {
                        $table = Engine_Api::_()->getDbtable('videos', 'ynvideochannel');
                        $video = $table->createRow();
                        $video->setFromArray($videoInformation);
                        if (!empty($videoInformation['large-thumbnail'])) {
                            $video->setPhoto($videoInformation['large-thumbnail']);
                        }
                        $video->save();
                        //Auth
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($video, $role, 'view', ($i <= $viewMax - 1));
                        }
                        foreach ($roles as $i => $role) {
                            $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax - 1));
                        }
                        $vCount++;
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
            }
            $channel->video_count += $vCount;
            $channel->save();
            $db->commit();

            // NOT CREATE FEED
//            $db->beginTransaction();
//            try {
//                if($vCount > 0) {
//                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $channel, 'ynvideochannel_channel_video_new', null, array('count' => $vCount));
//                    if ($action != null) {
//                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $channel);
//                        // Rebuild privacy
//                        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
//                        foreach ($actionTable->getActionsByObject($channel) as $action) {
//                            $actionTable->resetActivityBindings($action);
//                        }
//                    }
//                }
//                $db->commit();
//            } catch (Exception $e) {
//                $db->rollBack();
//                throw $e;
//            }
        }
    }
}