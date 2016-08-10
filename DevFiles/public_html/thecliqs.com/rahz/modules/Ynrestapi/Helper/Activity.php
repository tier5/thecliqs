<?php

class Ynrestapi_Helper_Activity extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('activity', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_owner()
    {
        $action = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($action->getSubject(), array('simple'));
    }

    public function field_content()
    {
        $content = $this->entry->getContent();
        $this->data['content'] = Ynrestapi_Helper_Utils::prepareHtmlHref($content);
    }

    public function field_attachments()
    {
        $action = $this->entry;
        $attachments = array();
        if ($action->getTypeInfo()->attachable && $action->attachment_count > 0 && count($action->getAttachments()) > 0) {
            foreach ($action->getAttachments() as $attachment) {
                $attachments[] = array(
                    'id' => $attachment->item->getIdentity(),
                    'type' => $attachment->item->getType(),
                    'title' => $attachment->item->getTitle(),
                    'description' => $attachment->item->getDescription(),
                    'img' => $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()),
                    'href' => Ynrestapi_Helper_Utils::prepareUrl($attachment->item->getHref()),
                );
            }
        }

        $this->data['attachments'] = $attachments;
    }

    public function field_can_comment()
    {
        $action = $this->entry;
        $canComment = ($action->getTypeInfo()->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment'));

        $this->data['can_comment'] = $canComment;
    }

    public function field_timestamp()
    {
        $action = $this->entry;
        $this->data['timestamp'] = $action->getTimeValue();
    }

    public function field_is_liked()
    {
        $action = $this->entry;
        $this->data['is_liked'] = $action->likes()->isLike($this->viewer());
    }

    public function field_can_delete()
    {
        $action = $this->entry;
        $this->data['can_delete'] = ($this->viewer()->getIdentity() && (
            $this->params['activity_moderate'] || (
                ($this->viewer()->getIdentity() == $this->params['activity_group']) || (
                    $this->params['allow_delete'] && (
                        ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                        ('user' == $action->object_type && $this->viewer()->getIdentity() == $action->object_id)
                    )
                )
            )
        ));
    }

    public function field_can_share()
    {
        $action = $this->entry;
        $this->data['can_share'] = ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity());
    }

    public function field_can_like()
    {
        $action = $this->entry;
        $this->data['can_like'] = $action->getTypeInfo()->commentable ? true : false;
    }

    public function field_total_like()
    {
        $action = $this->entry;
        $this->data['total_like'] = $action->likes()->getLikeCount();
    }

    public function field_user_liked()
    {
        $action = $this->entry;
        $this->data['user_liked'] = Ynrestapi_Helper_Meta::exportAll($action->likes()->getAllLikesUsers(), array('simple'));
    }

    public function field_total_comment()
    {
        $action = $this->entry;
        $this->data['total_comment'] = $action->comments()->getCommentCount();
    }

    public function field_comments()
    {
        $data = array();
        $action = $this->entry;
        $comments = $action->getComments();
        $commentLikes = $action->getCommentsLikes($comments, $this->viewer());
        foreach ($comments as $comment) {
            $data[] = array(
                'id' => $comment->comment_id,
                'owner' => Ynrestapi_Helper_Meta::exportOne(Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id), array('simple')),
                'body' => $comment->body,
                'date' => $comment->creation_date,
                'can_delete' => ($this->viewer()->getIdentity() &&
                    (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                        ($this->viewer()->getIdentity() == $comment->poster_id) ||
                        $this->params['activity_moderate'])),
                'is_liked' => !empty($commentLikes[$comment->comment_id]),
                'total_like' => $comment->likes()->getLikeCount(),
            );
        }

        $this->data['comments'] = $data;
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_owner();
        $this->field_content();
        $this->field_attachments();
        $this->field_timestamp();
        $this->field_is_liked();
        $this->field_can_like();
        $this->field_total_like();
        $this->field_can_comment();
        $this->field_total_comment();
        $this->field_can_delete();
        $this->field_can_share();
    }
}
