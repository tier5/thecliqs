<?php

class Ynrestapi_Helper_Forum_Post extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('forum', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_creation_date()
    {
        $post = $this->entry;
        $this->data['creation_date'] = $post->creation_date;
    }

    public function field_listing()
    {

    }

    public function field_detail()
    {

    }
}
