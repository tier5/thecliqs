<?php

class Ynrestapi_Helper_Notification extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('notification', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->notification_id;
    }

    public function field_type()
    {
        $this->data['type'] = $this->entry->type;
    }

    public function field_content()
    {
        $this->data['content'] = $this->entry->getContent();
    }

    public function field_read()
    {
        $this->data['read'] = $this->entry->read ? true : false;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_content();
        $this->field_owner();
        $this->field_read();
        $this->field_timestamp();
        $this->field_type();
    }
}
