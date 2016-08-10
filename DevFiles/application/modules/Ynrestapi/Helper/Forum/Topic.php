<?php

class Ynrestapi_Helper_Forum_Topic extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('forum', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_title()
    {
        $topic = $this->entry;
        $this->data['title'] = $topic->getTitle();
    }

    public function field_last_post()
    {
        $topic = $this->entry;
        $lastPost = $topic->getLastCreatedPost();
        if ($lastPost) {
            $this->data['last_post'] = Ynrestapi_Helper_Meta::exportOne($lastPost, array(
                'id',
                'owner',
                'creation_date',
            ));
        }
    }

    public function field_listing()
    {

    }

    public function field_detail()
    {

    }
}
