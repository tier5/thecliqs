<?php

class Ynrestapi_Helper_Event_Event extends Ynrestapi_Helper_Base
{

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_owner();
        $this->field_thumb();
        $this->field_member_count();
        $this->field_date();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_title();
        $this->field_description();
        $this->field_owner();
        $this->field_category();
        $this->field_thumb_profile();
        $this->field_member_count();
        $this->field_date_detail();
        $this->field_host();
        $this->field_location();
        $this->field_RSVPs();
        $this->field_can_edit();
        $this->field_can_delete();
        $this->field_can_request();
        $this->field_can_join();
        $this->field_can_leave();
        $this->field_can_cancel();
        $this->field_can_accept();
        $this->field_can_reject();
        $this->field_can_invite();
        $this->field_can_compose();
    }

    public function field_category()
    {
//        $this->data['category'] = $this->view->translate((string)$this->entry->categoryName());
        $item = $this->entry;
        $this->data['category'] = array();
        if (!empty($item->category_id)) {
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
            $category = $categoryTable->fetchRow($categoryTable->select()
                ->where('category_id = ?', $item->category_id));
            if ($category instanceof Core_Model_Item_Abstract &&
                !empty($category->title)) {
                $this->data['category'] = array(
                    'id' => $category->category_id,
                    'title' => $category->title,
                );
            }
        }
    }

    public function field_RSVPs()
    {
        $item = $this->entry;
        $this->data['attending_count'] = $item->getAttendingCount();
        $this->data['maybe_count'] = $item->getMaybeCount();
        $this->data['not_attending_count'] = $item->getNotAttendingCount();
        $this->data['awaiting_reply_count'] = $item->getAwaitingReplyCount();
    }

    public function field_host()
    {
        $this->data['host'] = $this->entry->host;
    }

    public function field_location()
    {
        $this->data['location'] = $this->entry->location;
    }

    public function field_thumb()
    {
        $this->data['thumb'] = $this->itemPhoto($this->entry, 'thumb.normal');
    }

    public function field_thumb_profile()
    {
        $this->data['thumb'] = $this->itemPhoto($this->entry, 'thumb.profile');
    }

    public function field_member_count()
    {
        $this->data['total_member'] = $this->view->locale()->toNumber($this->entry->membership()->getMemberCount());
    }

    public function field_date()
    {
        $this->data['start_datetime'] = $this->view->locale()->toDateTime($this->entry->starttime);
    }

    public function field_date_detail()
    {
        // Convert the dates for the viewer
        $startDateObject = new Zend_Date(strtotime($this->entry->starttime));
        $endDateObject = new Zend_Date(strtotime($this->entry->endtime));
        if( $this->viewer() && $this->viewer()->getIdentity() ) {
            $tz = $this->viewer()->timezone;
            $startDateObject->setTimezone($tz);
            $endDateObject->setTimezone($tz);
        }
        $locale = $this->view->locale();
        $this->data['start_date'] = $locale->toDate($startDateObject);
        $this->data['start_time'] = $locale->toTime($startDateObject);
        $this->data['end_date'] = $locale->toDate($endDateObject);
        $this->data['end_time'] = $locale->toTime($endDateObject);
    }

    public function field_can_invite()
    {
        $viewer = $this->viewer();
        $subject = $this->entry;

        $this->data['can_invite'] = $subject->authorization()->isAllowed($viewer, 'invite') ? true : false;
    }

    public function field_can_compose()
    {
        $viewer = $this->viewer();
        $subject = $this->entry;

        $this->data['can_compose'] = ($viewer->getIdentity() && $subject->isOwner($viewer));
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
                    'leave', // Leave Event
                );
            } else {
                return array(
                    'delete', // Delete Event
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
            throw new Group_Model_Exception('An error has occurred.');
        }
    }
}