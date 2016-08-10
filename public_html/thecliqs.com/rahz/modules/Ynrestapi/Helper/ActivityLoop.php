<?php

class Ynrestapi_Helper_ActivityLoop extends Ynrestapi_Helper_Base
{
    /**
     * @param  $actions
     * @param  array      $data
     * @return mixed
     */
    public function activityLoop($actions = null, array $data = array())
    {
        if (null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract))) {
            return '';
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = '';
        $group_owner = '';
        $group = '';
        try
        {
            if (Engine_Api::_()->core()->hasSubject('group')) {
                $group = Engine_Api::_()->core()->getSubject('group');
            }
        } catch (Exception $e) {
        }
        if ($group) {
            $table = Engine_Api::_()->getDbtable('groups', 'group');
            $select = $table->select()
                ->where('group_id = ?', $group->getIdentity())
                ->limit(1);

            $row = $table->fetchRow($select);
            $group_owner = $row['user_id'];
        }
        if ($viewer->getIdentity()) {
            $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
        }
        $data = array_merge($data, array(
            'actions' => $actions,
            'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
            'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
            'activity_group' => $group_owner,
            'activity_moderate' => $activity_moderate,
        ));

        return $data;
    }
}
