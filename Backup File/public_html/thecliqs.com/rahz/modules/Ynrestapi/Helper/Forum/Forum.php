<?php

class Ynrestapi_Helper_Forum_Forum extends Ynrestapi_Helper_Base
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
        $forum = $this->entry;
        $this->data['title'] = $forum->getTitle();
    }

    public function field_description()
    {
        $forum = $this->entry;
        $this->data['description'] = $forum->getDescription();
    }

    public function field_total_topic()
    {
        $forum = $this->entry;
        $this->data['total_topic'] = $forum->topic_count;
    }

    public function field_total_post()
    {
        $forum = $this->entry;
        $this->data['total_post'] = $forum->post_count;
    }

    public function field_last_topic()
    {
        $forum = $this->entry;
        $lastTopic = $forum->getLastUpdatedTopic();
        if ($lastTopic) {
            $this->data['last_topic'] = Ynrestapi_Helper_Meta::exportOne($lastTopic, array(
                'id',
                'title',
                'last_post',
            ));
        }
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_total_topic();
        $this->field_total_post();
        $this->field_last_topic();
    }

    public function field_detail()
    {

    }
}
