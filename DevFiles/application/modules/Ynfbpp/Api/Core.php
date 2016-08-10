<?php

class Ynfbpp_Api_Core
{

    /**
     * @property User_Model_User
     */
    private static $_viewer = NULL;

    public $view;

    public function __construct()
    {
        $view = Zend_Registry::get('Zend_View');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Ynfbpp/View/Helper', 'Ynfbpp_View_Helper_');
        $view -> addScriptPath(APPLICATION_PATH . '/application/modules/Ynfbpp/views/scripts');
        $this -> view = $view;
    }

    /**
     * get current viewer
     *  @return User_Model_User
     */
    public static function getViewer()
    {
        if (self::$_viewer == NULL)
        {
            self::$_viewer = Engine_Api::_() -> user() -> getViewer();
        }
        return self::$_viewer;
    }

    public function getUserPopupProfileFields()
    {
        $model = new Ynfbpp_Model_DbTable_Popup;
        $select = $model -> select() -> from($model -> info('name'), array(
            'field_id',
            'ordering'
        )) -> where('enabled=?', 1) -> order('ordering asc');
        return $model -> getAdapter() -> fetchPairs($select);
    }

    protected function _allowMessage($viewer, $subject)
    {
        // Not logged in
        if (!$viewer -> getIdentity() || $viewer -> getGuid(false) === $subject -> getGuid(false))
        {
            return false;
        }

        // Get setting?
        $permission = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'messages', 'create');
        if (Authorization_Api_Core::LEVEL_DISALLOW === $permission)
        {
            return false;
        }
        $messageAuth = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'messages', 'auth');
        if ($messageAuth == 'none')
        {
            return false;
        }
        else
        if ($messageAuth == 'friends')
        {
            // Get data
            $direction = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.direction', 1);
            if (!$direction)
            {
                //one way
                $friendship_status = $viewer -> membership() -> getRow($subject);
            }
            else
                $friendship_status = $subject -> membership() -> getRow($viewer);

            if (!$friendship_status || $friendship_status -> active == 0)
            {
                return false;
            }
        }
        return true;
    }

    public function _getUserActions($user, $viewer = null)
    {
        $actions = array();
        if (null === $viewer)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!$viewer || !$viewer -> getIdentity() || $user -> isSelf($viewer))
        {
            return $actions;
        }

        $direction = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.direction', 1);

        // Get data
        if (!$direction)
        {
            $row = $user -> membership() -> getRow($viewer);
        }
        else
            $row = $viewer -> membership() -> getRow($user);

        // Render

        // Check if friendship is allowed in the network
        $eligible = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.eligible', 2);
        if ($eligible == 0)
        {
            return $actions;
        }

        // check admin level setting if you can befriend people in your network
        else
        if ($eligible == 1)
        {

            $networkMembershipTable = Engine_Api::_() -> getDbtable('membership', 'network');
            $networkMembershipName = $networkMembershipTable -> info('name');

            $select = new Zend_Db_Select($networkMembershipTable -> getAdapter());
            $select -> from($networkMembershipName, 'user_id') -> join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null) -> where("`{$networkMembershipName}`.user_id = ?", $viewer -> getIdentity()) -> where("`{$networkMembershipName}_2`.user_id = ?", $user -> getIdentity());

            $data = $select -> query() -> fetch();

            if (empty($data))
            {
                return array();
            }
        }

        if (!$direction)
        {
            // one-way mode
            if (null === $row)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'add',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Follow'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/add.png')",
                    'class' => 'buttonlink smoothbox icon_friend_add',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Add Friend')
                ));
            }
            else
            if ($row -> resource_approved == 0)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'cancel',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Cancel Request'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/remove.png')",
                    'class' => 'buttonlink smoothbox icon_friend_cancel',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Cancel Follow Request')
                ));
            }
            else
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'remove',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Unfollow'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/remove.png')",
                    'class' => 'buttonlink smoothbox icon_friend_remove',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Unfollow')
                    
                ));
            }

        }
        else
        {
            // two-way mode
            if (null === $row)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'add',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Add Friend'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/add.png')",
                    'class' => 'buttonlink smoothbox icon_friend_add',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Add Friend')
                ));
            }
            else
            if ($row -> user_approved == 0)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'cancel',
                    'format' => 'smoothbox',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Cancel Request'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/remove.png')",
                    'class' => 'buttonlink smoothbox icon_friend_cancel',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Cancel Request')
                ));
            }
            else
            if ($row -> resource_approved == 0)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'format' => 'smoothbox',
                    'action' => 'confirm',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Accept Request'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/accepted.png')",
                    'class' => 'buttonlink smoothbox icon_friend_add',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Accept Request')
                ));
            }
            else
            if ($row -> active)
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'format' => 'smoothbox',
                    'action' => 'remove',
                    'user_id' => $user -> user_id
                ), $this -> view -> translate('Remove Friend'), array(
                    'style' => "background-image: url('application/modules/User/externals/images/friends/remove.png')",
                    'class' => 'buttonlink smoothbox icon_friend_remove',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                    'title' => $this -> view -> translate('Remove Friend')
                ));
                
            }
        }

        // check if allow message
        if($this->_allowMessage($viewer, $user)){
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'messages_general',
                'action' => 'compose',
                'to' => $user -> user_id
            ), $this -> view -> translate('Send Message'), array(
                'style' => "background-image: url('application/modules/Messages/externals/images/send.png')",
                'class' => 'buttonlink icon_message_send',
                'title' => $this -> view -> translate('Send Message')
            ));
        }

        // check to add reward point
        if (Engine_Api::_() -> hasModuleBootstrap('ynrewardpoints'))
        {
            $actions[] = $this -> view -> htmlLink(array(
                'route' => 'ynrewardpoints_general',
                'action' => 'give',
                'to' => $user -> user_id
            ), $this -> view -> translate('Points'), array(
                'style' => "padding-left:25px;background-image: url('application/modules/Ynrewardpoints/externals/images/rewads-give.png')",
                'class' => 'buttonlink',
                'title' => $this -> view -> translate('Give Reward Points')
            ));
        }

        return $actions;
    }

    public function _getEventActions($subject, $viewer = null)
    {

        $actions = array();

        if ($viewer == null)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!is_object($viewer) || !$viewer -> getIdentity())
        {
            return array();
        }

        $row = $subject -> membership() -> getRow($viewer);

        $actions[] = $this -> view -> htmlLink(array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject -> getType(),
            'id' => $subject -> getIdentity(),
            'route' => 'default',
            'format' => 'smoothbox',
        ), $this->view->translate('Share Event'), array(
            "style" => "background-image: url('application/modules/Event/externals/images/share.png')",
            'class' => 'buttonlink smoothbox',
            'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
        ));

        // Not yet associated at all
        if (null === $row)
        {
            if ($subject -> membership() -> isResourceApprovalRequired())
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'event_extended',
                    'controller' => 'member',
                    'action' => 'request',
                    'format' => 'smoothbox',
                    'event_id' => $subject -> getIdentity(),
                ), $this->view->translate('Request Event'), array(
                    "style" => "background-image: url('application/modules/Event/externals/images/member/join.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                ));
            }
            else
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'event_extended',
                    'controller' => 'member',
                    'format' => 'smoothbox',
                    'action' => 'join',
                    'event_id' => $subject -> getIdentity()
                ), $this->view->translate('Join Event'), array(
                    "style" => "background-image: url('application/modules/Event/externals/images/member/join.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                ));
            }
        }
        elseif ($row -> active)
        {
            if (!$subject -> isOwner($viewer))
            {
                $actions[] = $this -> view -> htmlLink(array(
                    'route' => 'event_extended',
                    'controller' => 'member',
                    'action' => 'leave',
                    'format' => 'smoothbox',
                    'event_id' => $subject -> getIdentity()
                ), $this->view->translate('Leave'), array(
                    "style" => "background-image: url('application/modules/Event/externals/images/member/leave.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
                ));
            }
            else
            {

            }
        }
        elseif (!$row -> resource_approved && $row -> user_approved)
        {
            $actions[] = $this -> view -> htmlLink(array(
            	'route' => 'event_extended',
                'controller' => 'member',
                'action' => 'cancel',
                'format' => 'smoothbox',
                'event_id' => $subject -> getIdentity()
            ), $this->view->translate('Cancel Invite Request'), array(
                "style" => "background-image: url('application/modules/Event/externals/images/member/cancel.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
            ));
        }
        elseif (!$row -> user_approved && $row -> resource_approved)
        {
            $actions[] = $this -> view -> htmlLink(array(
            	'route' => 'event_extended',
                'controller' => 'member',
                'action' => 'accept',
                'format' => 'smoothbox',
                'event_id' => $subject -> getIdentity()
            ), $this->view->translate('Accept Invite Request'), array(
                "style" => "background-image: url('application/modules/Event/externals/images/member/accept.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
            ));

            $actions[] = $this -> view -> htmlLink(array(
            	'route' => 'event_extended',
                'controller' => 'member',
                'action' => 'reject',
                'event_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
            ), $this->view->translate('Reject Invite Request'), array(
                "style" => "background-image: url('application/modules/Event/externals/images/member/reject.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;',
            ));
        }
        return $actions;
    }

    public function _getGroupActions($subject, $viewer = null)
    {
        $actions = array();

        if ($viewer == null)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!is_object($viewer) || $viewer -> getIdentity() == 0)
        {
            return $actions;
        }

        $actions[] = $this -> view -> htmlLink(array(
            'route' => 'default',
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject -> getType(),
            'id' => $subject -> getIdentity(),
            'format' => 'smoothbox',
        ), $this->view->translate('Share'), array(
            'style' => "background-image: url('application/modules/Group/externals/images/share.png')",
            'class' => 'buttonlink smoothbox',
            'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
        ));

        // invite
        if ($subject -> authorization() -> isAllowed($viewer, 'invite'))
        {

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'invite',
                'group_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
                'route' => 'group_extended'
            ), $this->view->translate('Invite'), array(
                'style' => "background-image: url('application/modules/Group/externals/images/member/invite.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
            ));
        }

        // membership
        $row = $subject -> membership() -> getRow($viewer);

        // Not yet associated at all
        if (null === $row)
        {
            if ($subject -> membership() -> isResourceApprovalRequired())
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'request',
                    'format' => 'smoothbox',
                    'group_id' => $subject -> getIdentity(),
                    'route' => 'group_extended'
                ), $this->view->translate('Request Membership'), array(
                    'style' => "background-image: url('./application/modules/Group/externals/images/member/join.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
                ));

            }
            else
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'join',
                    'format' => 'smoothbox',
                    'group_id' => $subject -> getIdentity(),
                    'route' => 'group_extended',
                ), $this->view->translate('Join'), array(
                    'style' => "background-image: url('application/modules/Group/externals/images/member/join.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
                ));
            }
        }
        elseif ($row -> active)
        {
            if (!$subject -> isOwner($viewer))
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'controller' => 'member',
                    'action' => 'leave',
                    'group_id' => $subject -> getIdentity(),
                    'format' => 'smoothbox',
                    'route' => 'group_extended'
                ), $this->view->translate('Leave'), array(
                    'style' => "background-image: url('application/modules/Group/externals/images/member/leave.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
                ));
            }
            else
            {

                $actions[] = $this -> view -> htmlLink(array(
                    'action' => 'delete',
                    'group_id' => $subject -> getIdentity(),
                    'format' => 'smoothbox',
                    'route' => 'group_specific'
                ), $this->view->translate('Delete Group'), array(
                    'style' => "background-image: url('application/modules/Group/externals/images/delete.png')",
                    'class' => 'buttonlink smoothbox',
                    'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
                ));
            }
        }
        elseif (!$row -> resource_approved && $row -> user_approved)
        {

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'cancel',
                'format' => 'smoothbox',
                'group_id' => $subject -> getIdentity(),
                'route' => 'group_extended'
            ), $this->view->translate('Cancel Membership Request'), array(
                'style' => "background-image: url('application/modules/Group/externals/images/member/cancel.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
            ));
        }
        elseif (!$row -> user_approved && $row -> resource_approved)
        {

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'accept',
                'group_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
                'route' => 'group_extended'
            ), $this->view->translate('Accept Membership Request'), array(
                'style' => "background-image: url('application/modules/Group/externals/images/member/accept.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
            ));

            $actions[] = $this -> view -> htmlLink(array(
                'controller' => 'member',
                'action' => 'reject',
                'group_id' => $subject -> getIdentity(),
                'format' => 'smoothbox',
                'route' => 'group_extended'
            ), $this->view->translate('Ignore Membership Request'), array(
                'style' => "background-image: url('application/modules/Group/externals/images/member/reject.png')",
                'class' => 'buttonlink smoothbox',
                'onclick' => 'ynfbpp.clearCached();Smoothbox.open(this);ynfbpp.closePopup();return false;'
            ));
        }
        return $actions;
    }

    public function renderUser($username)
    {

        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $model = Engine_Api::_() -> getDbTable('users', 'user');
        $select = $model -> select() -> where('username=?', (string)$username);
        $subject = $model -> fetchRow($select);

        if (!is_object($subject))
        {
            $subject = $model -> find((int)$username) -> current();
        }

        $this -> view -> subject = $subject;

        $actions = $this -> _getUserActions($subject, $viewer);

        // Don't render this if not authorized

        $this -> view -> actions = $actions;
        $onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
        $step = 900;
        $select = $onlineTable -> select() -> where('user_id=?', (int)$subject -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
        $online = $onlineTable -> fetchRow($select);
        //echo $select;
        $this -> view -> isSubjectOnline = is_object($online);

        //limit
        return $this -> view -> render('index/render-user.tpl');
    }

    public function renderGroup($groupId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('group', $groupId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

        // share action

        $this -> view -> actions = $actions = $this -> _getGroupActions($subject, $viewer);
        return $this -> view -> render('index/render-group.tpl');
    }

    public function renderEvent($eventId)
    {
        $this -> view -> subject = $subject = Engine_Api::_() -> getItem('event', $eventId);
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

        $this -> view -> actions = $actions = $this -> _getEventActions($subject, $viewer);
        return $this -> view -> render('index/render-event.tpl');
    }

    public function getJsonDataAction($type, $id)
    {

        $json = array(
            'error' => 0,
            'html' => '',
            'match_type' => $type,
            'match_id' => $id,
            'message' => ''
        );
        try
        {
            $method = 'render' . ucfirst($type);
            if (method_exists($this, $method))
            {
                $json['html'] = $this -> $method($id);
            }
        }
        catch (Exception $e)
        {
            $json['error'] = 1;
            $json['message'] = $e -> getMessage();
            if (APPLICATION_ENV == 'development')
            {
                throw $e;
            }
        }

        return Zend_Json::encode($json);
    }

}
