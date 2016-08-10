<?php

class Ynrestapi_Helper_Album_Album extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('album', 'ynrestapi');
    }

    public function field_id()
    {
        $album = $this->entry;
        $this->data['id'] = $album->album_id;
    }

    public function field_type()
    {
        $album = $this->entry;
        $this->data['type'] = $album->getType();
    }

    public function field_thumb()
    {
        $album = $this->entry;
        $this->data['thumb'] = Ynrestapi_Helper_Utils::prepareUrl($album->getPhotoUrl('thumb.normal'));
    }

    public function field_title()
    {
        $album = $this->entry;
        $this->data['title'] = ('' != trim($album->getTitle()) ? $album->getTitle() : Zend_Registry::get('Zend_Translate')->_('Untitled'));
    }

    public function field_description()
    {
        $album = $this->entry;
        $this->data['description'] = $album->getDescription();
    }

    public function field_owner()
    {
        $album = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($album->getOwner(), array('simple'));
    }

    public function field_total_photo()
    {
        $album = $this->entry;
        $this->data['total_photo'] = $album->count();
    }

    /**
     * @return null
     */
    public function field_can_upload()
    {
        $album = $this->entry;
        $mine = $album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer()) ? true : false;
        $canEdit = $this->getYnrestapiApi()->requireAuthIsValid($album, null, 'edit') ? true : false;
        $this->data['can_upload'] = $mine || $canEdit;
    }

    /**
     * @return null
     */
    public function field_can_editphotos()
    {
        $album = $this->entry;
        $canEdit = $this->getYnrestapiApi()->requireAuthIsValid($album, null, 'edit') ? true : false;
        $this->data['can_editphotos'] = $canEdit;
    }

    /**
     * @return null
     */
    public function field_can_edit()
    {
        $album = $this->entry;
        $canEdit = $this->getYnrestapiApi()->requireAuthIsValid($album, null, 'edit') ? true : false;
        $this->data['can_edit'] = $canEdit;
    }

    /**
     * @return null
     */
    public function field_can_delete()
    {
        $album = $this->entry;
        $canDelete = $this->getYnrestapiApi()->requireAuthIsValid($album, null, 'delete') ? true : false;
        $this->data['can_delete'] = $canDelete;
    }

    /**
     * @return null
     */
    public function field_photos()
    {
        if (!isset($this->params['photo_paginator']) || !isset($this->params['photo_fields'])) {
            return;
        }

        $this->data['photos'] = Ynrestapi_Helper_Meta::exportAll($this->params['photo_paginator'], $this->params['photo_fields']);
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_thumb();
        $this->field_title();
        $this->field_description();
        $this->field_owner();
        $this->field_total_photo();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_thumb();
        $this->field_title();
        $this->field_description();
        $this->field_owner();
        $this->field_total_photo();
        $this->field_photos();
    }
}
