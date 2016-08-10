<?php
class Ynfeed_Model_Action extends Activity_Model_Action {
    protected $_type = 'activity_action';

    public function getCommentObject() {
        $commentable = $this -> getTypeInfoCommentable();
        switch ($commentable) {
            // Comments linked to action item
            default :
            case 0 :
            case 1 :
            case 2 :
            case 3 :
                return $this -> getObject();
                break;

            // Comments linked to the first attachment
            case 4 :
                $attachments = $this -> getAttachments();
                if (!isset($attachments[0]) || !($attachments[0] -> item instanceof Core_Model_Item_Abstract)) {
                    return;
                }
                return $attachments[0] -> item;
                break;
        }
    }

    public function dislikes() {
        $commentable = $this -> getTypeInfoCommentable();

        switch ($commentable) {
            // Comments linked to subject
            case 2 :
                return Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> dislikes($this -> getSubject());
                break;

            // Comments linked to object
            case 3 :
                return Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> dislikes($this -> getObject()); ;
                break;

            // Comments linked to the first attachment
            case 4 :
                $attachments = $this -> getAttachments();
                if (isset($attachments[0])) {
                    return Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> dislikes($attachments[0] -> item);
                    break;
                }

            // Comments linked to action item
            default :
            case 0 :
            case 1 :
                return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('dislikes', 'yncomment'));
                break;
        }

        throw new Activity_Model_Exception('Dislikes handler undefined');
    }

    public function getTypeInfoCommentable() {
        $info = Engine_Api::_() -> getDbtable('actionTypes', 'activity') -> getActionType($this -> type);
        if ($info && in_array($this -> type, $this -> getCommentOnAttachmentType())) {
            $attachments = $this -> getAttachments();

            if (count($attachments) > 0 && isset($attachments[0]) && $attachments[0] -> item instanceof Core_Model_Item_Abstract && (method_exists($attachments[0] -> item, 'comments') || method_exists($attachments[0] -> item, 'likes'))) {
                return 4;
            }
        }
        return $info -> commentable;
    }

    public function getCommentOnAttachmentType() {
        return array('post', 'tagged', 'post_self', 'post_self_photo', 'post_self_video', 'post_self_music', 'post_self_link', 'user_cover_update', 'profile_photo_update', 'list_change_photo', 'recipe_change_photo', 'sitetagcheckin_post_self', 'sitetagcheckin_post', 'sitetagcheckin_tagged_new', 'sitepage_admin_cover_update', 'sitepage_cover_update', 'sitepage_profile_photo_update', 'sitebusiness_profile_photo_update', 'sitebusiness_admin_cover_update', 'sitebusiness_cover_update', 'sitegroup_admin_cover_update', 'sitegroup_cover_update', 'sitegroup_profile_photo_update', 'sitegroup_post', 'sitepage_post', 'sitebusiness_post', 'sitegroup_post_self', 'sitebusiness_post_self', 'sitepage_post_self', 'sitestoreproduct_admin_new', 'sitestoreproduct_new', 'siteevent_post', 'siteevent_post_parent', 'siteevent_change_photo_parent', 'siteevent_change_photo', 'siteeventdocument_new_parent', 'siteeventdocument_new', 'siteevent_cover_update_parent', 'siteevent_cover_update', 'siteevent_topic_create', 'siteevent_topic_create_parent', 'siteevent_video_new_parent', 'siteevent_video_new', 'siteevent_topic_reply', 'siteevent_topic_reply_parent', 'video_siteevent_parent', 'video_siteevent');
    }

    public function getComments($commentViewAll)
    {
        if( null !== $this->_comments ) {
          return $this->_comments;
        }
    
        $comments = $this->comments();
        $table = $comments->getReceiver();
        $comment_count = $comments->getCommentCount();
       
        if( $comment_count <= 0 ) {
          return;
        }
    
        $reverseOrder = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.commentreverseorder', false);
    
        // Always just get the last three comments
        $select = $comments->getCommentSelect();
    
        if (Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment())
        {
            $select->where('parent_comment_id =?', 0);
            $comment_count = count($select->query()->fetchAll());
            if( $comment_count <= 5 ) {
              $select->limit(5);      
            } else if( !$commentViewAll ) {
              if ($reverseOrder)
                $select->limit(5);
              else
                $select->limit(5, $comment_count - 5);
            }
        } else {
            if( $comment_count <= 5 ) {
              $select->limit(5);      
            } else if( !$commentViewAll ) {
              if ($reverseOrder)
                $select->limit(5);
              else
                $select->limit(5, $comment_count - 5);
            }
        }
        return $this->_comments = $table->fetchAll($select);
    } 
 
    public function removeReply($comment_parent_id)
    {
        if($replies = $this -> getReplies($comment_parent_id))
        {
            foreach ($replies as $reply) {
                $this -> removeReply($reply -> getIdentity());
            }
        }
        $this->comments()->removeComment($comment_parent_id);
    }

    public function getReplies($comment_id) {

        $comments = $this -> comments();
        $table = $comments -> getReceiver();
        $select = $comments -> getCommentSelect();

        $select -> where('parent_comment_id =?', $comment_id);
        $select -> reset('order');
        $select -> order(array('comment_id ASC'));
        return $table -> fetchAll($select);
    }
    
    public function getAllReplies($comment_id, &$count = 0)
    {
        if($replies = $this -> getReplies($comment_id))
        {
            $count = $count + count($replies);
            foreach ($replies as $reply) 
            {
                $this -> getAllReplies($reply -> getIdentity(), $count);
            }
        }
    }

}
