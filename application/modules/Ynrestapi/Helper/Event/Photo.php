<?php

class Ynrestapi_Helper_Event_Photo extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('event', 'ynrestapi');
    }

    public function field_id()
    {
        $photo = $this->entry;
        $this->data['id'] = $photo->photo_id;
    }

    public function field_type()
    {
        $photo = $this->entry;
        $this->data['type'] = $photo->getType();
    }

    public function field_thumb()
    {
        $photo = $this->entry;
        $this->data['thumb'] = Ynrestapi_Helper_Utils::prepareUrl($photo->getPhotoUrl('thumb.normal'));
    }

    public function field_img()
    {
        $photo = $this->entry;
        $this->data['img'] = Ynrestapi_Helper_Utils::prepareUrl($photo->getPhotoUrl());
    }

    public function field_title()
    {
        $photo = $this->entry;
        $this->data['title'] = $photo->getTitle();
    }

    public function field_description()
    {
        $photo = $this->entry;
        $this->data['description'] = $photo->getDescription();
    }

    public function field_date()
    {
        $photo = $this->entry;
        $this->data['date'] = $photo->modified_date;
    }

    public function field_event()
    {
        $eventFields = array(
            'id',
            'type',
            'title',
            'description',
            'owner',
        );

        $eventFields[] = 'total_photo';

        $event = $this->_getEvent();
        $this->data['event'] = Ynrestapi_Helper_Meta::exportOne($event, $eventFields);
    }

    public function field_photo_index()
    {
        $photo = $this->entry;
        $this->data['photo_index'] = $photo->getCollectionIndex();
    }

    public function field_next_photo()
    {
        $fields = array(
            'id',
            'thumb',
        );

        $nextPhoto = $this->_getNextPhoto();
        $this->data['next_photo'] = Ynrestapi_Helper_Meta::exportOne($nextPhoto, $fields);
    }

    public function field_previous_photo()
    {
        $fields = array(
            'id',
            'thumb',
        );

        $nextPhoto = $this->_getPreviousPhoto();
        $this->data['previous_photo'] = Ynrestapi_Helper_Meta::exportOne($nextPhoto, $fields);
    }

    public function field_can_tag()
    {
        $photo = $this->entry;
        $this->data['can_tag'] = method_exists($photo, 'tags');
    }

    public function field_can_edit()
    {
        $photo = $this->entry;
        $this->data['can_edit'] = $photo->authorization()->isAllowed($this->viewer(), 'edit') ? true : false;
    }

    public function field_can_delete()
    {
        $photo = $this->entry;
        $this->data['can_delete'] = $photo->authorization()->isAllowed($this->viewer(), 'delete') ? true : false;
    }

    public function field_can_share()
    {
        $this->data['can_share'] = $this->viewer()->getIdentity() ? true : false;
    }

    public function field_can_report()
    {
        $this->data['can_report'] = $this->viewer()->getIdentity() ? true : false;
    }

    public function field_can_make_profile_photo()
    {
        $this->data['can_make_profile_photo'] = $this->viewer()->getIdentity() ? true : false;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_thumb();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_img();
        $this->field_title();
        $this->field_description();
        $this->field_date();
        $this->field_event();
        $this->field_photo_index();
        $this->field_next_photo();
        $this->field_previous_photo();
        $this->field_can_tag();
        $this->field_can_edit();
        $this->field_can_delete();
        $this->field_can_share();
        $this->field_can_report();
        $this->field_can_make_profile_photo();
    }

    /**
     * @return mixed
     */
    private function _getEvent()
    {
        $photo = $this->entry;
        return $photo->getEvent();
    }

    /**
     * @return mixed
     */
    private function _getNextPhoto()
    {
        $photo = $this->entry;
        return $photo->getNextCollectible();
    }

    /**
     * @return mixed
     */
    private function _getPreviousPhoto()
    {
        $photo = $this->entry;
        return $photo->getPrevCollectible();
    }
}
