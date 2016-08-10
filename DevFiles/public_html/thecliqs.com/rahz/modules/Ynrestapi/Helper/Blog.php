<?php

class Ynrestapi_Helper_Blog extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('blog', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_title()
    {
        $this->data['title'] = $this->entry->getTitle();
    }

    public function field_owner()
    {
        $item = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($item->getOwner(), array('simple'));
    }

    public function field_date()
    {
        $item = $this->entry;
        $this->data['date'] = $item->creation_date;
    }

    public function field_body()
    {
        $item = $this->entry;
        $this->data['body'] = $item->body;
    }

    public function field_tags()
    {
        $blog = $this->entry;
        $blogTags = $blog->tags()->getTagMaps();

        $tags = array();
        if (count($blogTags)) {
            foreach ($blogTags as $tag) {
                $tags[] = array(
                    'id' => $tag->getTag()->tag_id,
                    'title' => $tag->getTag()->text,
                );
            }
        }

        $this->data['tags'] = $tags;
    }

    public function field_category()
    {
        $blog = $this->entry;
        $category = Engine_Api::_()->getDbtable('categories', 'blog')
            ->find($blog->category_id)->current();
        if ($category) {
            $this->data['category'] = array(
                'id' => $category->category_id,
                'title' => $category->category_name,
            );
        }
    }

    public function field_can_edit()
    {
        $blog = $this->entry;
        $this->data['can_edit'] = $this->getYnrestapiApi()->requireAuthIsValid($blog, null, 'edit') ? true : false;
    }

    public function field_can_delete()
    {
        $blog = $this->entry;
        $this->data['can_delete'] = $this->getYnrestapiApi()->requireAuthIsValid($blog, null, 'delete') ? true : false;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_owner();
        $this->field_date();
        $this->field_body();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_title();
        $this->field_owner();
        $this->field_date();
        $this->field_body();
        $this->field_tags();
        $this->field_category();
        $this->field_can_edit();
        $this->field_can_delete();
    }
}
