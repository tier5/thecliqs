<?php

class Ynrestapi_Helper_Comment extends Ynrestapi_Helper_Base
{
    public function field_owner()
    {
        $comment = $this->entry;
        $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($poster, array('simple'));
    }

    public function field_can_delete()
    {
        $comment = $this->entry;
        $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
        $this->data['can_delete'] = ($this->params['canDelete'] || $poster->isSelf($this->viewer()));
    }

    public function field_body()
    {
        $comment = $this->entry;
        $this->data['body'] = $comment->body;
    }

    public function field_date()
    {
        $comment = $this->entry;
        $this->data['date'] = $comment->creation_date;
    }

    public function field_is_liked()
    {
        $comment = $this->entry;
        $this->data['is_liked'] = $comment->likes()->isLike($this->viewer());
    }

    public function field_total_like()
    {
        $comment = $this->entry;
        $this->data['total_like'] = $comment->likes()->getLikeCount();
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_owner();
        $this->field_body();
        $this->field_date();
        $this->field_can_delete();
        $this->field_is_liked();
        $this->field_total_like();
    }
}
