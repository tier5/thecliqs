<?php

class Ynrestapi_Helper_Album_Photo extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('album', 'ynrestapi');
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

    public function field_album()
    {
        $albumFields = array(
            'id',
            'type',
            'title',
            'description',
            'owner',
        );

        if (isset($this->params['message_view']) && !$this->params['message_view']) {
            $albumFields[] = 'total_photo';
        }

        $album = $this->_getAlbum();
        $this->data['album'] = Ynrestapi_Helper_Meta::exportOne($album, $albumFields);
    }

    public function field_photo_index()
    {
        if (isset($this->params['message_view']) && !$this->params['message_view']) {
            $photo = $this->entry;
            $this->data['photo_index'] = $photo->getPhotoIndex();
        }
    }

    public function field_next_photo()
    {
        if (isset($this->params['message_view']) && !$this->params['message_view']) {
            $fields = array(
                'id',
                'thumb',
            );

            $nextPhoto = $this->_getNextPhoto();
            $this->data['next_photo'] = Ynrestapi_Helper_Meta::exportOne($nextPhoto, $fields);
        }
    }

    public function field_previous_photo()
    {
        if (isset($this->params['message_view']) && !$this->params['message_view']) {
            $fields = array(
                'id',
                'thumb',
            );

            $nextPhoto = $this->_getPreviousPhoto();
            $this->data['previous_photo'] = Ynrestapi_Helper_Meta::exportOne($nextPhoto, $fields);
        }
    }

    /**
     * @return null
     */
    public function field_can_tag()
    {
        if (isset($this->params['can_tag'])) {
            $this->data['can_tag'] = ($this->viewer()->getIdentity() && $this->params['can_tag']);
        }
    }

    public function field_can_edit()
    {
        if (isset($this->params['can_edit'])) {
            $this->data['can_edit'] = ($this->viewer()->getIdentity() && $this->params['can_edit']);
        }
    }

    public function field_can_delete()
    {
        if (isset($this->params['can_delete'])) {
            $this->data['can_delete'] = ($this->viewer()->getIdentity() && $this->params['can_delete']);
        }
    }

    public function field_can_share()
    {
        if (isset($this->params['message_view'])) {
            $this->data['can_share'] = ($this->viewer()->getIdentity() && !$this->params['message_view']);
        }
    }

    public function field_can_report()
    {
        if (isset($this->params['message_view'])) {
            $this->data['can_report'] = ($this->viewer()->getIdentity() && !$this->params['message_view']);
        }
    }

    public function field_can_make_profile_photo()
    {
        if (isset($this->params['message_view'])) {
            $this->data['can_make_profile_photo'] = ($this->viewer()->getIdentity() && !$this->params['message_view']);
        }
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
        $this->field_album();
        $this->field_photo_index();
        $this->field_next_photo();
        $this->field_previous_photo();
        $this->field_can_edit();
        $this->field_can_delete();
        $this->field_can_share();
        $this->field_can_report();
        $this->field_can_make_profile_photo();
    }

    /**
     * @return mixed
     */
    private function _getAlbum()
    {
        if (isset($this->params['album'])) {
            return $this->params['album'];
        }

        $photo = $this->entry;
        return $photo->getAlbum();
    }

    /**
     * @return mixed
     */
    private function _getNextPhoto()
    {
        if (isset($this->params['next_photo'])) {
            return $this->params['next_photo'];
        }

        $photo = $this->entry;
        return $photo->getNextPhoto();
    }

    /**
     * @return mixed
     */
    private function _getPreviousPhoto()
    {
        if (isset($this->params['previous_photo'])) {
            return $this->params['previous_photo'];
        }

        $photo = $this->entry;
        return $photo->getPreviousPhoto();
    }
}
