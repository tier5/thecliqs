<?php

class Ynrestapi_Helper_Music_Playlist extends Ynrestapi_Helper_Base
{
    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_owner()
    {
        $entry = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($entry->getOwner(), array('simple'));
    }

    public function field_thumb()
    {
        $item = $this->entry;
        if ($item->photo_id) {
            $thumb = $this->itemPhoto($item, 'thumb.normal');
        } else {
            $thumb = Ynrestapi_Helper_Utils::getBaseUrl() . '/application/modules/Music/externals/images/nophoto_playlist_main.png';
        }
        $this->data['thumb'] = $thumb;
    }

    public function field_date()
    {
        $item = $this->entry;
        $this->data['date'] = strip_tags($this->view->timestamp($item->creation_date));
    }

    public function field_songs()
    {
        $songs = $this->entry->getSongs();
        $this->data['songs'] = Ynrestapi_Helper_Meta::exportAll($songs, array('listing'));
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_id();
        $this->field_owner();
        $this->field_date();
        $this->field_creation_date();
        $this->field_can_edit();
        $this->field_can_delete();
        $this->field_total_like();
        $this->field_total_comment();
        $this->field_total_view();
        $this->field_thumb();
        $this->field_songs();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_id();
        $this->field_owner();
        $this->field_date();
        $this->field_creation_date();
        $this->field_can_edit();
        $this->field_can_delete();
        $this->field_total_like();
        $this->field_total_comment();
        $this->field_total_view();
        $this->field_thumb();
        $this->field_songs();
    }
}