<?php

class Ynrestapi_Helper_Music_PlaylistSong extends Ynrestapi_Helper_Base
{
    public function field_song_url()
    {
        $this->data['song_url'] = Ynrestapi_Helper_Utils::prepareUrl($this->entry->getFilePath());
    }

    public function field_play_count()
    {
        $this->data['play_count'] = $this->entry->play_count;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_song_url();
        $this->field_play_count();
    }
}