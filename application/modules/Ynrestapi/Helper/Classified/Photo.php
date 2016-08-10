<?php

class Ynrestapi_Helper_Classified_Photo extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('classified', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }
}
