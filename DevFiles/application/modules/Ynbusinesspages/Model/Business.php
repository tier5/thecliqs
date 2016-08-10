<?php

class Ynbusinesspages_Model_Business extends Core_Model_Item_Abstract
{

    protected $_type = 'ynbusinesspages_business';
    protected $_parent_type = 'user';

    /*----- Get Business desciption Function ----*/
    public function getDescription()
    {
        $view = Zend_Registry::get('Zend_View');
        $tmp_description = strip_tags($this->short_description);
        $description = $view->string()->truncate($tmp_description, 150);
        return $description;
    }

    public function membership()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'ynbusinesspages'));
    }

    public function tags()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    public function getTitle()
    {
        if (isset($this->name)) {
            return $this->name;
        }
        return null;
    }

    public function getHref($params = array())
    {
        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => 'ynbusinesspages_profile',
            'reset' => true,
            'id' => $this->getIdentity(),
            'slug' => $slug,
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }


    public function checkRated()
    {
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_review');
        $viewer = Engine_Api::_()->user()->getViewer();
        $rName = $table->info('name');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->where('business_id = ?', $this->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity())
            ->limit(1);
        $row = $table->fetchAll($select);

        if (count($row) > 0)
            return true;
        return false;
    }


    public function delete()
    {
        $this -> _delete();
        //delete all claims if exsist
        $tableClaim = Engine_Api::_()->getItemTable('ynbusinesspages_claimrequest');
        $tableClaim->denyAllClaims($this->getIdentity());

        //delete actions and attachments
        $streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
        $streamTbl->delete('(`object_id` = ' . $this->getIdentity() . ' AND `object_type` = "ynbusinesspages_business")');
        $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
        $activityTbl->delete('(`object_id` = ' . $this->getIdentity() . ' AND `object_type` = "ynbusinesspages_business")');
        $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
        $attachmentTbl->delete('(`id` = ' . $this->getIdentity() . ' AND `type` = "ynbusinesspages_business")');
        $this->changeStatus('deleted');
        $this->removeList();

        $this->deleted = true;
        $this -> _postDelete();

        $this->save();
    }

    public function removeList()
    {
        $this->membership()->removeAllMembers();
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $lists = $listTbl->getListByBusiness($this, false);
        if (count($lists)) {
            foreach ($lists as $list) {
                $list->delete();
            }
        }
    }

    public function changeStatus($status)
    {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $this->status = $status;
            $this->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function getMainLocation($getRow = false)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $locationTbl = Engine_Api::_()->getDbTable('locations', 'ynbusinesspages');
        $select = $locationTbl->select()->where('business_id = ?', $this->getIdentity())
            ->where('main = 1')->limit(1);
        $row = $locationTbl->fetchRow($select);
        if ($getRow) {
            return $row;
        }
        if (isset($row))
            return $row->location;
        else
            return $translate->translate('Unknown');
    }

    public function getMainLocationObject($getRow = true)
    {
        return $this->getMainLocation($getRow);
    }

    public function getCategoryIds()
    {
        $mapTbl = Engine_Api::_()->getDbTable('categorymaps', 'ynbusinesspages');
        $categoriesMaps = $mapTbl->getCategoriesByBusinessId($this->getIdentity());
        $categoryIds = array();
        foreach ($categoriesMaps as $map) {
            $categoryIds[] = $map->category_id;
        }
        return $categoryIds;
    }

    public function getCategories()
    {
        $categoryIds = $this->getCategoryIds();
        $categoryTbl = Engine_Api::_()->getItemTable('ynbusinesspages_category');
        $select = $categoryTbl->select()->where("category_id IN (?)", $categoryIds);
        return $categoryTbl->fetchAll($select);
    }

    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else
            if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else
                if (is_string($photo) && file_exists($photo)) {
                    $file = $photo;
                } else {
                    throw new Ynbusinesspages_Model_Exception('invalid argument passed to setPhoto');
                }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'ynbusinesspages_business',
            'parent_id' => $this->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();
        $angle = 0;
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($file);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }
        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file);
        if ($angle != 0)
            $image->rotate($angle);
        $image->resize(720, 720)->write($path . '/m_' . $name)->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file);
        if ($angle != 0)
            $image->rotate($angle);
        $image->resize(200, 400)->write($path . '/p_' . $name)->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        @$image->open($file);
        if ($angle != 0)
            $image->rotate($angle);
        $image->resize(140, 105)->write($path . '/in_' . $name)->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $iMain->file_id;
        $this->save();

        return $this;
    }

    public function getSingletonAlbum()
    {
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_album');
        $select = $table->select()->where('business_id = ?', $this->getIdentity())->order('album_id ASC')->limit(1);

        $album = $table->fetchRow($select);

        if (null === $album) {
            $album = $table->createRow();
            $album->setFromArray(array('business_id' => $this->getIdentity()));
            $album->save();
        }

        return $album;
    }

    public function inCompare()
    {
        return Engine_Api::_()->ynbusinesspages()->checkBusinessInCompare($this->getIdentity());
    }

    public function getMainCategoryId()
    {
        $mapTbl = Engine_Api::_()->getDbTable('categorymaps', 'ynbusinesspages');
        $select = $mapTbl->select()->where("business_id = ?", $this->getIdentity())->where("main = ?", 1);
        $categoryMap = $mapTbl->fetchRow($select);
        return $categoryMap->category_id;
    }

    public function getMainCategory()
    {
        $id = $this->getMainCategoryId();
        return Engine_Api::_()->getItem('ynbusinesspages_category', $id);
    }

    public function getRating()
    {
        $tableReview = Engine_Api::_()->getItemTable('ynbusinesspages_review');
        $rows = $tableReview->fetchAll($tableReview->select()->where('business_id = ?', $this->getIdentity()));
        $total_rate_number = 0;
        foreach ($rows as $row) {
            $total_rate_number += $row['rate_number'];
        }
        if (count($rows) > 0) {
            return round(($total_rate_number / count($rows)), 2);
        } else {
            return 0;
        }
    }

    public function getReviewCount()
    {
        $tableReview = Engine_Api::_()->getItemTable('ynbusinesspages_review');
        $select = $tableReview->select()->from($tableReview->info('name'), 'COUNT(*) AS count')->where('business_id = ?', $this->getIdentity());
        return $select->query()->fetchColumn(0);
    }

    public function getMemberCount()
    {
        $members = $this->membership()->getMembers(true);
        return count($members);
    }

    public function getCommentCount()
    {
        $actionTable = Engine_Api::_()->getItemTable('activity_action');
        $arrTypes = array('status', 'post', 'post_self');
        $select = $actionTable->select()
            ->from($actionTable, array('count(*) as amount'))
            ->where('type IN (?)', $arrTypes)
            ->where('object_type = ?', 'ynbusinesspages_business')
            ->where('object_id = ?', $this->getIdentity());
        $row = $actionTable->fetchRow($select);
        return ($row->amount);

    }

    public function getFollowerCount()
    {
        $followTable = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
        $usersFollow = $followTable->getUsersFollow($this->getIdentity());
        return count($usersFollow);
    }

    public function getPagesCount()
    {
        $wikiTable = Engine_Api::_()->getDbtable('pages', 'ynwiki');
        $select = $wikiTable->select();
        $select->from($wikiTable, array('count(*) as amount'));
        $select->where('parent_type = ?', 'ynbusinesspages_business')
            ->where('parent_id = ?', $this->business_id);
        $row = $wikiTable->fetchRow($select);
        return ($row->amount);
    }

    public function getDiscussionsCount()
    {
        $topicTable = Engine_Api::_()->getDbtable('topics', 'ynbusinesspages');
        $select = $topicTable->select();
        $select->from($topicTable, array('count(*) as amount'));
        $select->where('business_id = ?', $this->business_id);
        $row = $topicTable->fetchRow($select);
        return ($row->amount);
    }

    public function getFilesCount()
    {
        $filesTable = Engine_Api::_()->getDbtable('files', 'ynfilesharing');
        $select = $filesTable->select();

        $tableFolders = Engine_Api::_()->getDbTable('folders', 'ynfilesharing');
        $selectFolders = $tableFolders->select()
            ->from($tableFolders->info('name'), 'folder_id')
            ->where('parent_type = ?', 'ynbusinesspages_business')
            ->where('parent_id = ?', $this->business_id);
        $folderIds = $tableFolders->fetchAll($selectFolders);
        $arr_folderIds = array();
        foreach ($folderIds as $id) {
            $arr_folderIds[] = $id->folder_id;
        }
        if (count($arr_folderIds) > 0) {
            $select
                ->from($filesTable, array('count(*) as amount'))
                ->where('folder_id IN (?)', $arr_folderIds);
            $row = $filesTable->fetchRow($select);
            return ($row->amount);
        } else {
            return 0;
        }
    }

    public function getAlbumPhotosCount()
    {
        $photoTable = Engine_Api::_()->getDbtable('photos', 'ynbusinesspages');
        $select = $photoTable->select();
        $select->from($photoTable, array('count(*) as amount'));
        $select->where('business_id = ?', $this->business_id);
        $row = $photoTable->fetchRow($select);
        return ($row->amount);
    }

    public function countItemMapping($arr_type)
    {
        $mapTable = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages');
        $count = $mapTable->countItem($arr_type, $this->business_id);
        return $count;
    }

    public function getTotalShare()
    {

        $tableAttachments = Engine_Api::_()->getDbTable('attachments', 'activity');
        $selectAttachments = $tableAttachments->select()
            ->from($tableAttachments->info('name'), 'action_id')
            ->where('type = ?', 'ynbusinesspages_business')
            ->where('id = ?', $this->getIdentity());

        $actionIds = $tableAttachments->fetchAll($selectAttachments);
        $arr_actionIds = array();
        foreach ($actionIds as $id) {
            $arr_actionIds[] = $id->action_id;
        }
        if (count($arr_actionIds) > 0) {
            $tableAction = Engine_Api::_()->getItemTable('activity_action');
            $selectAcion = $tableAction->select()
                ->from($tableAction, array('count(*) as amount'))
                ->where('type = ?', 'share')
                ->where('action_id IN (?)', $arr_actionIds);
            $row = $tableAction->fetchRow($selectAcion);
            return ($row->amount);
        } else {
            return 0;
        }
    }

    public function getLatestReview()
    {
        $table = Engine_Api::_()->getDbTable('reviews', 'ynbusinesspages');
        $select = $table->select()->where('business_id = ?', $this->getIdentity())->order('creation_date DESC');
        $review = $table->fetchRow($select);
        return $review;
    }

    public function getAllLocations()
    {
        $locationTbl = Engine_Api::_()->getDbTable('locations', 'ynbusinesspages');
        $select = $locationTbl->select()->where('business_id = ?', $this->getIdentity());
        $rows = $locationTbl->fetchAll($select);
        return $rows;
    }

    public function getOperatingHours()
    {
        $hourTbl = Engine_Api::_()->getDbTable('operatinghours', 'ynbusinesspages');
        $select = $hourTbl->select()->where('business_id = ?', $this->getIdentity());
        $rows = $hourTbl->fetchAll($select);
        return $rows;
    }

    public function isAdmin($user)
    {
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $listTblName = $listTbl->info('name');

        $itemTbl = Engine_Api::_()->getDbTable('listItems', 'ynbusinesspages');
        $itemTblName = $itemTbl->info('name');

        $select = $listTbl
            ->select()
            ->from($listTblName)->setIntegrityCheck(false)
            ->join($itemTblName, "{$listTblName}.list_id = {$itemTblName}.list_id AND {$itemTblName}.child_id = {$user->getIdentity()}")
            ->where("{$listTblName}.type = 'admin'")
            ->where("{$listTblName}.owner_id = '?'", $this->getIdentity())
            ->limit(1);
        $list = $listTbl->fetchRow($select);
        return (!is_null($list));
    }

    public function getAdminList()
    {
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $select = $listTbl->select()
            ->where("owner_id = ?", $this->getIdentity())
            ->where("type = 'admin'")
            ->limit(1);
        return $listTbl->fetchRow($select);
    }

    public function getMemberList()
    {
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $select = $listTbl->select()
            ->where("owner_id = ?", $this->getIdentity())
            ->where("type = ?", 'member')
            ->limit(1);
        return $listTbl->fetchRow($select);
    }

    public function getRegisteredList()
    {
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $select = $listTbl->select()
            ->where("owner_id = ?", $this->getIdentity())
            ->where("type = ?", 'registered')
            ->limit(1);
        return $listTbl->fetchRow($select);
    }

    public function getNonRegisteredList()
    {
        $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $select = $listTbl->select()
            ->where("owner_id = ?", $this->getIdentity())
            ->where("type = ?", 'non-registered')
            ->limit(1);
        return $listTbl->fetchRow($select);
    }

    public function hasReviewed($user)
    {
        if (is_null($user)) {
            $user = Engine_Api::_()->user()->getViewer();
        }
        if (is_null($user)) return true;
        $table = Engine_Api::_()->getDbTable('reviews', 'ynbusinesspages');
        $select = $table->select()->where('business_id = ?', $this->getIdentity())->where('user_id = ?', $user->getIdentity());
        $review = $table->fetchRow($select);
        return (is_null($review)) ? false : true;
    }

    public function getPackage()
    {
        $table = Engine_Api::_()->getDbTable('packages', 'ynbusinesspages');
        if (!empty($this->package_id)) {
            $select = $table->select()->where('package_id = ?', $this->package_id)->limit(1);
            return $table->fetchRow($select);
        } else {
            return new Ynbusinesspages_Model_Package(array());
        }
    }

    public function sendNotificationToFollowers($type)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $followTable = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
        $usersFollow = $followTable->getUsersFollow($this->getIdentity());
        foreach ($usersFollow as $userFollow) {
            if ($userFollow->getIdentity() != $viewer->getIdentity()) {
                $notificationSettingsTable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');
                if ($notificationSettingsTable->checkEnabledNotification($userFollow, 'ynbusinesspages_follow_business') && !empty($userFollow->email)) {
                    $notifyApi->addNotification($userFollow, $viewer, $this, 'ynbusinesspages_follow_business', array('type' => $type));
                }
            }
        }
    }

    public function approve($userId)
    {
        $user = Engine_Api::_()->user()->getUser($userId);
        if (!$user->getIdentity()) {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $db = $this->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $this->membership()->setResourceApproved($user);
            $memberList = $this->getMemberList();
            if (!is_null($memberList)) {
                if (!$memberList->has($user)) {
                    $memberList->add($user);
                }
            }

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $this, 'ynbusinesspages_accepted');
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function reject($userId)
    {
        $user = Engine_Api::_()->user()->getUser($userId);
        if (!$user->getIdentity()) {
            return false;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $db = $this->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $this->membership()->removeMember($user);
            $memberList = $this->getMemberList();
            if (!is_null($memberList)) {
                if ($memberList->has($user)) {
                    $memberList->remove($user);
                }
            }

            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($viewer, $this, 'ynbusinesspages_invite');
            if ($notification) {
                $notification->mitigated = true;
                $notification->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function cancel($userId)
    {
        $user = Engine_Api::_()->user()->getUser($userId);
        $viewer = Engine_Api::_()->user()->getViewer();
        $db = $this->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $this->membership()->removeMember($user);
            $memberList = $this->getMemberList();
            if (!is_null($memberList)) {
                if ($memberList->has($user)) {
                    $memberList->remove($user);
                }
            }

            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($this->getOwner(), $this, 'ynbusinesspages_approve');
            if ($notification) {
                $notification->delete();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function isEditable()
    {
        if ($this->is_claimed) {
            return true;
        }
        return $this->isAllowed('edit');
    }

    public function isDeletable()
    {
        return $this->authorization()->isAllowed(null, 'delete');
    }

    public function isEndable()
    {
        return $this->authorization()->isAllowed(null, 'end');
    }

    public function isViewable()
    {
        if ($this->is_claimed) {
            return true;
        }
        return $this->isAllowed('view');
    }

    public function isCommentable()
    {
        if ($this->is_claimed) {
            return true;
        }
        return $this->isAllowed('comment');
    }

    public function insertSampleList()
    {
        $listTble = Engine_Api::_()->getItemTable('ynbusinesspages_list');
        $listTble->insertSampleData($this);
    }

    /**
     *
     * Checking user permission belongs to specific role.
     * @param $action String
     * @param $user Core_Model_Item_Abstract
     * @param $object Core_Model_Item_Abstract
     * action can be:
     * - invite
     * - approve
     * - view
     * - comment
     * - album_create
     * - album_delete
     * ...
     *
     * @return BOOLEAN
     */
    public function isAllowed($action, $user = null, $object = null)
    {
        return Engine_Api::_()->ynbusinesspages()->isAllowed($this, $action, $user, $object);
    }

    public function isCheckedIn($user)
    {
        $checkInTbl = Engine_Api::_()->getDbTable('checkin', 'ynbusinesspages');
        return $checkInTbl->isCheckedIn($user, $this);
    }

    public function checkin($user)
    {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $checkInTbl = Engine_Api::_()->getDbTable('checkin', 'ynbusinesspages');
            $row = $checkInTbl->createRow();
            $row->user_id = $user->getIdentity();
            $row->business_id = $this->getIdentity();
            $row->save();

            $this->checkin_count++;
            $this->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

    }

    public function transferOwner($user)
    {
        if (is_null($user) || !$user->getIdentity()) {
            return false;
        }
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $adminList = $this->getAdminList();
            $memberList = $this->getMemberList();
            $businessOwner = $this->getOwner();

            // ADD USER TO ADMIN ROLE
            if (!$adminList->has($user)) {
                $adminList->add($user);
                if (!$this->membership()->isMember($user)) {
                    $this->membership()->addMember($user)->setUserApproved($user)->setResourceApproved($user);
                }
                $this->membership()->getMemberInfo($user)->setFromArray(array('list_id' => $adminList->getIdentity()))->save();
            }

            // REMOVE USER FROM MEMBER ROLE
            if ($memberList->has($user)) {
                $memberList->remove($user);
            }

            // AND MOVE OLD ADMIN TO MEMBER ROLE
            if (!$memberList->has($businessOwner)) {
                $memberList->add($businessOwner);
            }
            $this->membership()->getMemberInfo($businessOwner)->setFromArray(array('list_id' => $memberList->getIdentity()))->save();

            // REMOVE OLD ADMIN
            if ($adminList->has($businessOwner)) {
                $adminList->remove($businessOwner);
            }

            $this->user_id = $user->getIdentity();
            if ($this->status == 'unclaimed' || $this->status == 'claimed') {
                $this->status = 'draft';
                $this->is_claimed = false;
            }
            $this->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function getContactForm()
    {
        $table = Engine_Api::_()->getDbTable('contacts', 'ynbusinesspages');
        $select = $table->select()->where('business_id = ?', $this->getIdentity())->limit(1);
        $row = $table->fetchRow($select);
        return $row;
    }

    public function getContactQuestionFields()
    {
        $fieldMetaTbl = new Ynbusinesspages_Model_DbTable_Meta();
        $questionFields = $fieldMetaTbl->getFields($this);
        return $questionFields;
    }

    public function deleteItem($type = null, $item_id = null)
    {
        $tableName = $this->info('name');
        $db = $this->getAdapter();
        $db->beginTransaction();
        try {
            $db->delete($tableName, array(
                'type = ?' => $params['type'],
                'item_id = ?' => $params['item_id']
            ));
            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
        return "true";
    }

    public function getFirstCover()
    {
        $coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
        $select = $coverTbl
            ->select()
            ->where("business_id = ? ", $this->getIdentity())
            ->order("order ASC")
            ->limit(1);
        return $coverTbl->fetchRow($select);
    }

    public function isClaimedByUser()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $tableClaimRequest = Engine_Api::_()->getDbTable('claimrequests', 'ynbusinesspages');
            $request = $tableClaimRequest->getClaimRequest($viewer->getIdentity(), $this->getIdentity());
            if ($request) {
                return true;
            }
        }
        return false;
    }
}