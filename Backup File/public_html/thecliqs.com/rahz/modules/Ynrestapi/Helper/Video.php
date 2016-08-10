<?php

class Ynrestapi_Helper_Video extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('video', 'ynrestapi');
    }

    public function field_id()
    {
        $item = $this->entry;
        $this->data['id'] = $item->video_id;
    }

    public function field_title()
    {
        $item = $this->entry;
        $this->data['title'] = $item->getTitle();
    }

    public function field_description()
    {
        $item = $this->entry;
        $this->data['description'] = $item->description;
    }

    public function field_duration()
    {
        $item = $this->entry;
        if ($item->duration >= 3600) {
            $duration = gmdate('H:i:s', $item->duration);
        } else {
            $duration = gmdate('i:s', $item->duration);
        }
        $this->data['duration'] = $duration;
    }

    public function field_thumb()
    {
        $item = $this->entry;
        if ($item->photo_id) {
            $thumb = $this->itemPhoto($item, 'thumb.normal');
        } else {
            $thumb = Ynrestapi_Helper_Utils::getBaseUrl() . '/application/modules/Video/externals/images/video.png';
        }
        $this->data['thumb'] = $thumb;
    }

    public function field_owner()
    {
        $item = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($item->getOwner(), array('simple'));
    }

    public function field_date()
    {
        $item = $this->entry;
        $this->data['date'] = $item->creation_date;
    }

    public function field_total_comment()
    {
        $item = $this->entry;
        $this->data['total_comment'] = $item->comments()->getCommentCount();
    }

    public function field_total_like()
    {
        $item = $this->entry;
        $this->data['total_like'] = $item->likes()->getLikeCount();
    }

    public function field_total_view()
    {
        $item = $this->entry;
        $this->data['total_view'] = $item->view_count;
    }

    public function field_total_vote()
    {
        $item = $this->entry;
        $this->data['total_vote'] = Engine_Api::_()->video()->ratingCount($item->getIdentity());
    }

    public function field_rating()
    {
        $item = $this->entry;
        $this->data['rating'] = $item->rating;
    }

    public function field_is_rated()
    {
        $item = $this->entry;
        $this->data['is_rated'] = Engine_Api::_()->video()->checkRated($item->getIdentity(), $this->viewer()->getIdentity()) ? true : false;
    }

    public function field_status()
    {
        $item = $this->entry;

        $statuses = array(
            0 => array(
                'title' => 'to_be_processed',
                'description' => Zend_Registry::get('Zend_Translate')->_('Your video is in queue to be processed - you will be notified when it is ready to be viewed.'),
            ),
            2 => array(
                'title' => 'being_processed',
                'description' => Zend_Registry::get('Zend_Translate')->_('Your video is currently being processed - you will be notified when it is ready to be viewed.'),
            ),
            3 => array(
                'title' => 'conversion_failed',
                'description' => Zend_Registry::get('Zend_Translate')->_('Video conversion failed. Please try uploading again.'),
            ),
            4 => array(
                'title' => 'conversion_failed',
                'description' => Zend_Registry::get('Zend_Translate')->_('Video conversion failed. Video format is not supported by FFMPEG. Please try again.'),
            ),
            5 => array(
                'title' => 'conversion_failed',
                'description' => Zend_Registry::get('Zend_Translate')->_('Video conversion failed. Audio files are not supported. Please try again.'),
            ),
            7 => array(
                'title' => 'conversion_failed',
                'description' => Zend_Registry::get('Zend_Translate')->_('Video conversion failed. You may be over the site upload limit.  Try uploading a smaller file, or delete some files to free up space.'),
            ),
        );

        if (array_key_exists($item->status, $statuses)) {
            $status = $statuses[$item->status]['title'];
        } else {
            $status = 'processed';
        }

        $this->data['status'] = $status;
    }

    public function field_video_type()
    {
        $item = $this->entry;
        $types = array(
            1 => 'youtube',
            2 => 'vimeo',
            3 => 'upload',
        );
        $this->data['video_type'] = $types[$item->type];
    }

    public function field_video_src()
    {
        $item = $this->entry;
        $src = '';
        switch ($item->type) {
            case 1:
                $src = 'https://www.youtube.com/embed/' . $item->code;
                break;

            case 2:
                $src = 'https://player.vimeo.com/video/' . $item->code;
                break;

            case 3:
                if ($item->status == 1) {
                    if (!empty($item->file_id)) {
                        $storage_file = Engine_Api::_()->getItem('storage_file', $item->file_id);
                        if ($storage_file) {
                            $src = $storage_file->map();
                        }
                    }
                }
                break;
        }

        $this->data['video_src'] = $src;
    }

    public function field_category()
    {
        $item = $this->entry;
        $dataCategory = null;
        if ($item->category_id) {
            $category = Engine_Api::_()->video()->getCategory($item->category_id);
            $dataCategory = array(
                'id' => $category->category_id,
                'title' => $category->category_name,
            );
        }
        $this->data['category'] = $dataCategory;
    }

    public function field_tags()
    {
        $item = $this->entry;
        $videoTags = $item->tags()->getTagMaps();
        $dataTags = array();
        if (count($videoTags)) {
            foreach ($videoTags as $tag) {
                $dataTags[] = array(
                    'id' => $tag->getTag()->tag_id,
                    'title' => $tag->getTag()->text,
                );
            }
        }
        $this->data['tags'] = $dataTags;
    }

    public function field_can_edit()
    {
        $video = $this->entry;
        $this->data['can_edit'] = $this->getYnrestapiApi()->requireAuthIsValid($video, null, 'edit') ? true : false;
    }

    public function field_can_delete()
    {
        $video = $this->entry;
        $this->data['can_delete'] = $this->getYnrestapiApi()->requireAuthIsValid($video, null, 'delete') ? true : false;
    }

    /**
     * @return null
     */
    public function field_can_embed()
    {
        if (!isset($this->params['can_embed'])) {
            return;
        }
        $this->data['can_embed'] = $this->params['can_embed'] ? true : false;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_duration();
        $this->field_thumb();
        $this->field_owner();
        $this->field_total_view();
        $this->field_rating();
    }

    public function field_manage()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_duration();
        $this->field_thumb();
        $this->field_owner();
        $this->field_date();
        $this->field_total_comment();
        $this->field_total_like();
        $this->field_total_view();
        $this->field_rating();
        $this->field_status();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_duration();
        $this->field_thumb();
        $this->field_owner();
        $this->field_date();
        $this->field_total_comment();
        $this->field_total_like();
        $this->field_total_view();
        $this->field_total_vote();
        $this->field_rating();
        $this->field_is_rated();
        $this->field_status();
        $this->field_video_type();
        $this->field_video_src();
        $this->field_category();
        $this->field_tags();
    }
}
