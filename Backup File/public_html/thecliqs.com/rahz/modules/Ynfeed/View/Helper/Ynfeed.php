<?php
class Ynfeed_View_Helper_Ynfeed extends Zend_View_Helper_Abstract
{
  public function ynfeed(Activity_Model_Action $action = null, array $data = array(), $method = null, $show_all_comments = false)
  {
    if( null === $action )
    {
      return '';
    }
    $activity_moderate = "";
    $viewer = Engine_Api::_()->user()->getViewer();
    if($viewer -> getIdentity())
    {
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
            ->getAllowed('user', $viewer->level_id, 'activity');
    }

    $form = new Activity_Form_Comment();
    $data = array_merge($data, array(
      'actions' => array($action),
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
      'viewAllComments' => isset($data['viewAllComments']) && $data['viewAllComments'] ? $data['viewAllComments'] : $show_all_comments,
      'allowSaveFeed' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeed_savefeed', true),
      'onlyactivity' => 1,
    ));
    if (Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment())
    {
        $data = array_merge($data, array('replyForm' => new Yncomment_Form_Reply()));
        if ($method == 'update') 
        {
            if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.commentreverseorder', false)) 
            {
                return $this->view->partial('_yncomment_activityComments.tpl', 'yncomment', $data);
            } 
            else 
            {
                 return $this->view->partial('_yncomment_activityComments_reverse_chronological.tpl', 'yncomment', $data);
            }
        } 
        else 
        {
            if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.commentreverseorder', false)) 
            {
                return $this->view->partial('_yncomment_activityText.tpl', 'yncomment', $data);
            } 
            else 
            {
                 return $this->view->partial('_yncomment_activityText_reverse_chronological.tpl', 'yncomment', $data);
            }
        }
    }
    else {
        if($method == 'update')
        {
          return $this->view->partial(
          '_activityComments.tpl',
          'activity',
          $data
        );
        }
        else{
          return $this->view->partial(
            '_activityText.tpl',
            'ynfeed',
            $data
            );
          }
        }
    }
}