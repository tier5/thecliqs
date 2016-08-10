<?php

class Ynrestapi_Helper_Classified_Classified extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('classified', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_thumb()
    {
        $item = $this->entry;
        $this->data['thumb'] = $this->itemPhoto($item, 'thumb.normal');
    }

    public function field_title()
    {
        $item = $this->entry;
        $this->data['title'] = $item->getTitle();
    }

    public function field_is_closed()
    {
        $this->data['is_closed'] = $this->_isClosed();
    }

    public function field_creation_date()
    {
        $item = $this->entry;
        $this->data['creation_date'] = $item->creation_date;
    }

    public function field_owner()
    {
        $item = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($item->getOwner(), array('simple'));
    }

    public function field_price()
    {
        $item = $this->entry;
        $view = $this->view;
        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item);
        foreach ($fieldStructure as $index => $map) {
            $field = $map->getChild();
            if ($field->type == 'currency') {
                $value = $field->getValue($this->entry);
                $this->data['price'] = $view->locale()->toCurrency($value->value);
            }
        }
    }

    public function field_location()
    {
        $item = $this->entry;
        $view = $this->view;
        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item);
        foreach ($fieldStructure as $index => $map) {
            $field = $map->getChild();
            if ($field->type == 'location') {
                $value = $field->getValue($this->entry);
                $this->data['location'] = !empty($value->value) ? $value->value : '';
            }
        }
    }

    public function field_body()
    {
        $item = $this->entry;
        $this->data['body'] = $item->body;
    }

    public function field_category()
    {
        $classified = $this->entry;
        if ($classified->category_id) {
            $categoryObject = Engine_Api::_()->getDbtable('categories', 'classified')
                ->find($classified->category_id)->current();
            $this->data['category'] = array(
                'id' => $categoryObject->getIdentity(),
                'title' => $categoryObject->category_name,
            );
        }
    }

    public function field_tags()
    {
        $classified = $this->entry;
        $classifiedTags = $classified->tags()->getTagMaps();
        $tags = array();
        foreach ($classifiedTags as $tag) {
            $tags[] = array(
                'id' => $tag->getTag()->getIdentity(),
                'title' => $tag->getTag()->text,
            );
        }
        $this->data['tags'] = $tags;
    }

    public function field_photos()
    {
        $classified = $this->entry;
        $album = $classified->getSingletonAlbum();
        $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber(1);
        $paginator->setItemCountPerPage(100);
        $photos = array();
        foreach ($paginator as $photo) {
            $photos[] = array(
                'id' => $photo->file_id,
                'description' => $photo->getDescription(),
                'img' => Ynrestapi_Helper_Utils::prepareUrl($photo->getPhotoUrl()),
                'is_main' => ($classified->photo_id == $photo->file_id),
            );
        }
        $this->data['photos'] = $photos;
    }

    public function field_can_edit()
    {
        $this->data['can_edit'] = $this->_canEdit();
    }

    public function field_can_add_photos()
    {
        $item = $this->entry;
        $this->data['can_add_photos'] = Engine_Api::_()->authorization()->getPermission($this->viewer()->level_id, 'classified', 'photo') ? true : false;
    }

    public function field_can_open()
    {
        $this->data['can_open'] = $this->_isClosed() && $this->_canEdit();
    }

    public function field_can_close()
    {
        $this->data['can_close'] = !$this->_isClosed() && $this->_canEdit();
    }

    public function field_can_delete()
    {
        $item = $this->entry;
        $this->data['can_delete'] = $this->getYnrestapiApi()->requireAuthIsValid($item, null, 'delete') ? true : false;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_thumb();
        $this->field_title();
        $this->field_is_closed();
        $this->field_creation_date();
        $this->field_owner();
        $this->field_price();
        $this->field_location();
        $this->field_body();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_thumb();
        $this->field_title();
        $this->field_is_closed();
        $this->field_creation_date();
        $this->field_owner();
        $this->field_price();
        $this->field_location();
        $this->field_body();
        $this->field_category();
        $this->field_tags();
        $this->field_photos();
    }

    /**
     * @return mixed
     */
    private function _isClosed()
    {
        $item = $this->entry;
        return $item->closed ? true : false;
    }

    /**
     * @return mixed
     */
    private function _canEdit()
    {
        $item = $this->entry;
        return $this->getYnrestapiApi()->requireAuthIsValid($item, null, 'edit') ? true : false;
    }
}
