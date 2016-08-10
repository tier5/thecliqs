<?php

class Ynrestapi_Helper_Group_Group extends Ynrestapi_Helper_Base
{
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('group', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_thumb()
    {
        $group = $this->entry;
        $this->data['thumb'] = $this->itemPhoto($group, 'thumb.normal');
    }

    public function field_title()
    {
        $group = $this->entry;
        $this->data['title'] = $group->getTitle();
    }

    public function field_category()
    {
        $group = $this->entry;
        if (!empty($group->category_id) &&
            ($category = $group->getCategory()) instanceof Core_Model_Item_Abstract &&
            !empty($category->title)) {
            $this->data['category'] = array(
                'id' => $category->category_id,
                'title' => $category->title,
            );
        }
    }

    public function field_owner()
    {
        $group = $this->entry;
        $this->data['owner'] = Ynrestapi_Helper_Meta::exportOne($group->getOwner(), array('simple'));
    }

    public function field_description()
    {
        $group = $this->entry;
        $this->data['description'] = $group->getDescription();
    }

    /**
     * @return null
     */
    public function field_staff()
    {
        $subject = $this->entry;
        $viewer = $this->viewer();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return;
        }

        $ids = array();
        $ids[] = $subject->getOwner()->getIdentity();
        $list = $subject->getOfficerList();
        foreach ($list->getAll() as $listiteminfo) {
            $ids[] = $listiteminfo->child_id;
        }

        $staff = array();
        foreach ($ids as $id) {
            $user = Engine_Api::_()->getItem('user', $id);
            $staff[] = array(
                'membership' => $subject->membership()->getMemberInfo($user),
                'user' => $user,
            );
        }

        $data = array();
        foreach ($staff as $info) {
            $user = Ynrestapi_Helper_Meta::exportOne($info['user'], array('simple'));
            if ($subject->isOwner($info['user'])) {
                $user['membership'] = !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : Zend_Registry::get('Zend_Translate')->_('owner');
            } else {
                $user['membership'] = !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : Zend_Registry::get('Zend_Translate')->_('officer');
            }
            $data[] = $user;
        }

        $this->data['staff'] = $data;
    }

    public function field_total_view()
    {
        $group = $this->entry;
        $this->data['total_view'] = $group->view_count;
    }

    public function field_total_member()
    {
        $group = $this->entry;
        $this->data['total_member'] = $group->membership()->getMemberCount();
    }

    public function field_updated_date()
    {
        $group = $this->entry;
        $this->data['updated_date'] = $group->modified_date;
    }

    public function field_can_edit()
    {
        $group = $this->entry;
        $this->data['can_edit'] = $this->getYnrestapiApi()->requireAuthIsValid($group, null, 'edit') ? true : false;
    }

    public function field_can_delete()
    {
        $group = $this->entry;
        $this->data['can_delete'] = $this->getYnrestapiApi()->requireAuthIsValid($group, null, 'delete') ? true : false;
    }

    public function field_can_request()
    {
        $this->data['can_request'] = $this->isMembershipOption('request');
    }

    public function field_can_join()
    {
        $this->data['can_join'] = $this->isMembershipOption('join');
    }

    public function field_can_leave()
    {
        $this->data['can_leave'] = $this->isMembershipOption('leave');
    }

    public function field_can_cancel()
    {
        $this->data['can_cancel'] = $this->isMembershipOption('cancel');
    }

    public function field_can_accept()
    {
        $this->data['can_accept'] = $this->isMembershipOption('accept');
    }

    public function field_can_reject()
    {
        $this->data['can_reject'] = $this->isMembershipOption('reject');
    }

    /**
     * @param $option
     */
    public function isMembershipOption($option)
    {
        $membershipOptions = $this->getMembershipOptions();
        return (false !== array_search($option, $membershipOptions));
    }

    public function getMembershipOptions()
    {
        $viewer = $this->viewer();
        $subject = $this->entry;

        if (!$viewer->getIdentity()) {
            return array();
        }

        $row = $subject->membership()->getRow($viewer);

        // Not yet associated at all
        if (null === $row) {
            if ($subject->membership()->isResourceApprovalRequired()) {
                return array(
                    'request', // Request Membership
                );
            } else {
                return array(
                    'join', // Join Group
                );
            }
        }

        // Full member
        // @todo consider owner
        else if ($row->active) {
            if (!$subject->isOwner($viewer)) {
                return array(
                    'leave', // Leave Group
                );
            } else {
                return array(
                    'delete', // Delete Group
                );
            }
        } else if (!$row->resource_approved && $row->user_approved) {
            return array(
                'cancel', // Cancel Membership Request
            );
        } else if (!$row->user_approved && $row->resource_approved) {
            return array(
                'accept', // Accept Membership Request
                'reject', // Ignore Membership Request
            );
        } else {
            throw new Group_Model_Exception('Wow, something really strange happened.');
        }
    }

    /**
     * @return mixed
     */
    public function field_can_invite()
    {
        $viewer = $this->viewer();
        $subject = $this->entry;

        $this->data['can_invite'] = $subject->authorization()->isAllowed($viewer, 'invite') ? true : false;
    }

    public function field_can_message_members()
    {
        $viewer = $this->viewer();
        $subject = $this->entry;

        $this->data['can_message_members'] = ($viewer->getIdentity() && $subject->isOwner($viewer));
    }

    public function field_total_photo()
    {
        $group = $this->entry;
        $album = $group->getSingletonAlbum();
        $this->data['total_photo'] = $album->count();
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_thumb();
        $this->field_title();
        $this->field_owner();
        $this->field_description();
        $this->field_total_member();
        $this->field_can_edit();
        $this->field_can_delete();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_thumb();
        $this->field_title();
        $this->field_category();
        $this->field_owner();
        $this->field_description();
        $this->field_staff();
        $this->field_total_view();
        $this->field_total_member();
        $this->field_updated_date();
        $this->field_can_edit();
        $this->field_can_delete();
        $this->field_can_request();
        $this->field_can_join();
        $this->field_can_leave();
        $this->field_can_cancel();
        $this->field_can_accept();
        $this->field_can_reject();
        $this->field_can_invite();
        $this->field_can_message_members();
    }
}
