<?php

class Yncontest_Model_Contest extends Core_Model_Item_Abstract
{

    const IMAGE_WIDTH = 720;
    const IMAGE_HEIGHT = 720;


    const THUMB_WIDTH = 140;
    const THUMB_HEIGHT = 105;

    private static $_perms = array(
        "draft" => array("editcontests", "publish", "deletecontests"),
        "pending" => array("editcontests", "deletecontests"),
        "show" => array("close"),
        //"denied" => array("editcontests","deletecontests"),
        "denied" => array("deletecontests"),
        "close" => array()
    );
    private static $_permsIsAdmin = array(
        "draft" => array("editcontests", "publish", "deletecontests"),
        "pending" => array("approved", "denied"),
        "show" => array("editcontests", "deletecontests"),
        //"denied" => array("editcontests","deletecontests"),
        "denied" => array("deletecontests"),
        "close" => array("editcontests", "deletecontests")
    );
    private static $_noperms = array(
        0 => "publish",
        1 => "approved",
        2 => "denied",
        3 => "close",
    );

    protected $_type = 'contest';
    protected $_parent_type = 'user';

    public function getEntriesByContest()
    {
        $entries = Engine_Api::_()->getDbTable('entries', 'yncontest')->getEntriesContest2(array('contestID' => $this->getIdentity(), 'approve_status' => 1));
        return $entries;
    }

    public function checkAllow($params = array())
    {
        if (empty($params['user_id'])) {
            $user = Engine_Api::_()->user()->getViewer();
        } else {
            $user = Engine_Api::_()->user()->getUser($params['user_id']);
        }
        if ($user->isAdminOnly()) {
            $perms = self::$_permsIsAdmin[$this->contest_status];
        } else {
            $perms = self::$_perms[$this->contest_status];
        }

        if ($this->authorization()->isAllowed($user, $params['action'])) {
            return true;
        }

        if (in_array($params['action'], self::$_noperms))
            if ($this->checkIsOwner()) return true;

        return false;
    }

    public function sendNotMailOwner($user1, $user2, $keyNot, $keyAct)
    {

        if (!empty($keyNot)) {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($user1, $user2, $this, $keyNot);
        }
        if (!empty($keyAct)) {
            $action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user1, $this, $keyAct);
            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $this);
            }
        }
    }

    public function sendNotMailFollwer($admin, $keyNot, $params)
    {

        if (!empty($keyNot)) {

            $follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
            $followUsers = $follow_table->getUserFolowContest($this->contest_id);
            foreach ($followUsers as $followUser) {
                //send notification
                $user = Engine_Api::_()->user()->getUser($followUser->user_id);
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($user, $admin, $this, $keyNot, $params);
                // 				//send email
                // 				$params["contest_link"] = $contest_link;
                // 				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                // 				$notifyApi -> addNotification($user,$admin, $this, $keyNot);
                // 				if(!empty($user->email))
                // 					Engine_Api::_() -> getApi('mail', 'yncontest') -> send($user->email, $keyNot, $params);
            }


        }

    }

    public function getMemberContest()
    {

        $table = Engine_Api::_()->getDbTable('membership', 'yncontest');

        $ids = array();
        $select = $table->select()->where("resource_id = ?", $this->contest_id)->where("active = ? ", true);
        //print_r($select);die;
        $results = $table->fetchAll($select);

        foreach ($results as $result) {
            $ids[] = $result->user_id;
        }

        return $ids;
    }


    public function deniedContest()
    {
        $this->approve_status = 'denied';
        //$contest->approve_status = 'approved';
        $this->contest_status = 'draft';
        $this->save();
        //send notification
        $admin = Engine_Api::_()->user()->getViewer(1);
        $owner = Engine_Api::_()->user()->getUser($this->user_id);
        $this->sendNotMailOwner($owner, $admin, 'contest_denied', null);
    }

    public function sendNotOwner($key)
    {
        $owner = Engine_Api::_()->user()->getUser($this->user_id);
        $admin = Engine_Api::_()->user()->getUser(1);
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $admin, $this, $key);
    }

    public function closeContest($admin)
    {

        //update status contest
        if ($this->contest_status != 'close') {
            $this->contest_status = 'close';
            $this->save();
            //send notification & mail
            $user1 = Engine_Api::_()->user()->getUser($this->user_id);
            //$admin = Engine_Api::_() -> user() -> getUser(1);
            $this->sendNotMailOwner($user1, $admin, 'close_contest', null);


            //send notify to members when contes close
            $members = $this->membership()->getMembers($this->getIdentity(), true);
            foreach ($members as $member) {
                if (!$this->isOwner($member))
                    $this->sendNotMailOwner($member, $user1, 'close_contest_members', null);
            }

            //change entries win when contest closed
            if ($this->award_number > 0)
                $entries = Engine_Api::_()->getItemTable('yncontest_entries')->getEntryByvote(array(
                    'contestID' => $this->contest_id,
                    'award_number' => $this->award_number,
                ));
            else $entries = array();

            foreach ($entries as $entry) {
                if ($entry->entry_status == 'published' && $entry->approve_status == 'approved') {
                    $entry->entry_status = 'win';
                    $entry->save();
                    $user = Engine_Api::_()->user()->getUser($entry->user_id);
                    //send notify to entry win by vote
                    $entry->sendNotMailOwner($user, $entry, 'entry_win_vote', null, array('vote_desc' => $this->vote_desc));

                    //send notify to follower
                    //$this->sendNotMailFollwer($entry, 'entry_win_vote_f',array('vote_desc' => $this->vote_desc));

                    $follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
                    $followUsers = $follow_table->getUserFolowContest($this->contest_id);
                    foreach ($followUsers as $followUser) {
                        //send notification
                        if ($user->user_id != $followUser->user_id) {
                            $f_user = Engine_Api::_()->user()->getUser($followUser->user_id);
                            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                            $notifyApi->addNotification($f_user, $entry, $this, 'entry_win_vote_f', array('vote_desc' => $this->vote_desc));
                        }
                    }
                }
            }

            $entries = Engine_Api::_()->getItemTable('yncontest_entries')->getEntryByOwner(array(
                'contestID' => $this->contest_id,

            ));

            foreach ($entries as $entry) {
                if (($entry->entry_status == 'published' || $entry->entry_status == 'win') && $entry->approve_status == 'approved') {
                    $entry->entry_status = 'win';
                    $entry->save();
                    $user = Engine_Api::_()->user()->getUser($entry->user_id);
                    //send notify to entry win by vote
                    $entry->sendNotMailOwner($user, $entry, 'entry_win_vote', null, array('vote_desc' => $this->reason_desc));

                    //send notify to follower
                    //$this->sendNotMailFollwer($entry, 'entry_win_vote_f',array('vote_desc' => $this->reason_desc));

                    $follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
                    $followUsers = $follow_table->getUserFolowContest($this->contest_id);
                    foreach ($followUsers as $followUser) {
                        //send notification
                        if ($user->user_id != $followUser->user_id) {
                            $f_user = Engine_Api::_()->user()->getUser($followUser->user_id);
                            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                            $notifyApi->addNotification($f_user, $entry, $this, 'entry_win_vote_f', array('vote_desc' => $this->reason_desc));
                        }
                    }
                }
            }

        }


    }

    public function sendNotFollow($key)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
        $followUsers = $follow_table->getUserFolowContest($this->contest_id);
        foreach ($followUsers as $followUser) {
            $user = Engine_Api::_()->user()->getUser($followUser->user_id);
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($user, $viewer, $this, $key);
        }
    }

    public function checkAllowVote($members, $entry)
    {
        /*$viewer = Engine_Api::_()->user()->getViewer();

        $ruleVote= Engine_Api::_()->yncontest()->checkRule(array(
                'contestId'=>$this->contest_id,
                'key' => 'voteentries',
        ));

        if($ruleVote && $members->member_status == 'approved' && $this->contest_status == 'published' && $entry->checkVote()  && $this->authorization()->isAllowed($viewer,'voteentries')){
        return true;
        }
        return false;*/
        return true;
    }

    public function getDescription()
    {
        if (isset($this->summary)) {
            $tmpBody = strip_tags($this->summary);
            return (Engine_String::strlen($tmpBody) > 155 ? Engine_String::substr($tmpBody, 0, 155) . '...' : $tmpBody);
            //return $this->summary;
        }
        return null;
    }

    public function getTitle()
    {

        if (isset($this->contest_name)) {
            return $this->contest_name;
        }
        return null;
    }

    public function checkIsOwner()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($this->isOwner($viewer) || $viewer->isAdminOnly())
            return true;
        return false;
    }

    public function getContestOwner()
    {
        $contest = Engine_Api::_()->getItem('yncontest_contest', $this->contest_id);
        $user = Engine_Api::_()->user()->getUser($contest->user_id);
        return $user;
    }


    protected function _delete()
    {


        parent::_delete();
        // Delete yncontest_announcements
        $table = Engine_Api::_()->getItemTable('yncontest_announcements');
        $selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($selectSubItems) as $subItem) {
            $subItem->delete();
        }
        // Delete yncontest_awards
        // 		$table = Engine_Api::_()->getItemTable('yncontest_awards');
        // 		$selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        // 		foreach ($table->fetchAll($selectSubItems) as $subItem) {
        // 			$subItem->delete();
        // 		}
        // Delete yncontest_entries
        $table = Engine_Api::_()->getItemTable('yncontest_entries');
        $selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($selectSubItems) as $subItem) {
            $subItem->delete();
        }
        // Delete yncontest_entriesfavourites
        // 		$table = Engine_Api::_()->getItemTable('yncontest_entriesfavourites');
        // 		$selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        // 		foreach ($table->fetchAll($selectSubItems) as $subItem) {
        // 			$subItem->delete();
        // 		}

        // Delete yncontest_favourites
        $table = Engine_Api::_()->getItemTable('yncontest_favourite');
        $selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($selectSubItems) as $subItem) {
            $subItem->delete();
        }
        // Delete yncontest_follows
        $table = Engine_Api::_()->getItemTable('yncontest_follows');
        $selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($selectSubItems) as $subItem) {
            $subItem->delete();
        }
        // Delete yncontest_listitems
        // Delete yncontest_lists
        // Delete yncontest_membership
        // 		$table = Engine_Api::_()->getItemTable('yncontest_membership');
        // 		$selectSubItems = $table->select()->where('resource_id = ?', $this->getIdentity());
        // 		foreach ($table->fetchAll($selectSubItems) as $subItem) {
        // 			$subItem->delete();
        // 		}
        // Delete yncontest_members
        // 		$table = Engine_Api::_()->getItemTable('yncontest_members');
        // 		$selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        // 		foreach ($table->fetchAll($selectSubItems) as $subItem) {
        // 			$subItem->delete();
        // 		}
        // Delete yncontest_rules
        // 		$table = Engine_Api::_()->getItemTable('yncontest_rules');
        // 		$selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        // 		foreach ($table->fetchAll($selectSubItems) as $subItem) {
        // 			$subItem->delete();
        // 		}
        // Delete yncontest_settings
        $table = Engine_Api::_()->getItemTable('yncontest_settings');
        $selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($selectSubItems) as $subItem) {
            $subItem->delete();
        }
        // Delete yncontest_transactions
        // $table = Engine_Api::_()->getItemTable('yncontest_transactions');
        // $selectSubItems = $table->select()->where('contest_id = ?', $this->getIdentity());
        // foreach ($table->fetchAll($selectSubItems) as $subItem) {
        // $subItem->delete();
        // }


    }

    public function getSlug($str = null)
    {
        $str = $this->getTitle();
        if (strlen($str) > 32) {
            $str = Engine_String::substr($str, 0, 32) . '...';
        }
        $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
        $str = strtolower($str);
        $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        $str = trim($str, '-');
        if (!$str) {
            $str = '-';
        }
        return $str;
    }

    public function getHref($params = array())
    {
        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => 'yncontest_mycontest',
            'action' => 'view',
            'reset' => true,
            'contestId' => $this->contest_id,
            'slug' => $slug,
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }

    public function isFavourited($user_id = 0)
    {
        if ($user_id == 0) {
            return false;
        }
        $sql = "select favourite_id from engine4_yncontest_favourites where user_id=$user_id and contest_id={$this->contest_id}";
        $row = Engine_Db_Table::getDefaultAdapter()->fetchOne($sql);
        return (bool)$row;
    }

    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Yncontest_Model_Exception('invalid argument passed to setPhoto');
        }
        //add album
        $album = $this->getSingletonAlbum();


        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array('album_id' => $album->getIdentity(), 'parent_type' => 'contest', 'parent_id' => $this->getIdentity());

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)->write($path . '/m_' . $name)->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)->resize(200, 140)->write($path . '/p_' . $name)->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)->resize(170, 140)->write($path . '/in_' . $name)->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)->write($path . '/is_' . $name)->destroy();

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
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        //add photo_id in contest_photos
        $params = array(
            // We can set them now since only one album is allowed
            'collection_id' => $album->getIdentity(),
            'album_id' => $album->getIdentity(),
            'contest_id' => $this->getIdentity(),
            'user_id' => $this->user_id,
            'file_id' => $iMain->file_id,
            'photo_id' => $iMain->file_id,
        );
        $row = Engine_Api::_()->getDbtable('photos', 'Yncontest')->createRow();
        $row->setFromArray($params);
        $row->save();

        return $this;
    }

    public function checkPublish()
    {
        $settings = Engine_Api::_()->getDbTable('settings', 'yncontest')->getSettingByContest($this->contest_id);
        if (count($settings) == 0) return false;
        return true;
    }

    public function getDefaultService()
    {
        $serviceTable = Engine_Api::_()->getDbtable('services', 'storage');
        $nameService = $serviceTable->info('name');
        $select = $serviceTable->select()->where("$nameService.servicetype_id = ?", 1)->where("$nameService.enabled = ?", 1)->where("$nameService.default = ?", 1);
        return $serviceTable->fetchRow($select);
    }

    public function createPhoto($album_id, $file)
    {
        //@TODO

        if (filter_var($file->map(), FILTER_VALIDATE_URL) === FALSE) {
            $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
            $url = $http . $_SERVER['SERVER_NAME'] . $file->map();
        } else {
            $url = $file->map();
        }

        $storage = Engine_Api::_()->storage();

        // Get image info and resize
        $name = basename($file->storage_path);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';


        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($url)->write($path . '/m_' . $name)->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($url)->resize(200, 140)->write($path . '/t_' . $name)->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($url)->resize(140, 105)->write($path . '/n_' . $name)->destroy();

        // Resize image (normal1)
        $image = Engine_Image::factory();
        $image->open($url)->resize(85, 65)->write($path . '/n1_' . $name)->destroy();
        $params = array('album_id' => $album_id, 'parent_type' => 'album_photo', 'parent_id' => $this->getIdentity());


        //save file

        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/t_' . $name, $params);
        $iNormal = $storage->create($path . '/n_' . $name, $params);
        $iNormal1 = $storage->create($path . '/n1_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iNormal, 'thumb.normal');
        $iMain->bridge($iNormal1, 'thumb.normal1');
        // Remove temp files
        @unlink($path . '/m_' . $name);
        @unlink($path . '/t_' . $name);
        @unlink($path . '/n_' . $name);
        @unlink($path . '/n1_' . $name);

        //save in photo tables
        //$params = array();
        if ($album_id != 0) {
            $params = array('album_id' => $album_id, 'owner_type' => 'user', 'owner_id' => $this->user_id, 'file_id' => $iMain->getIdentity());

            $albumPlugin = Engine_Api::_()->yncontest()->getPluginsAlbum();

            $photo = Engine_Api::_()->getItemTable($albumPlugin . '_photo')->createRow();
            $photo->setFromArray($params);
            $photo->save();
            $photo->order = $photo->photo_id;
            $photo->save();

            return $photo;
        }
        return $iMain->getIdentity();

    }

    public function createVideo($entry_name, $file)
    {


        // --------------------------
        $date = new DateTime();
        $scriptName = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $url = "http://" . $_SERVER['SERVER_NAME'] . $scriptName . "/" . $file->storage_path;
        $content = file_get_contents($url);
        $dir = dirname($_SERVER['SCRIPT_FILENAME']);
        $fp = fopen($dir . "/public/contest/" . $date->getTimestamp() . $file->name, 'w');
        fwrite($fp, $content);
        fclose($fp);

        return $scriptName . "/public/contest/" . $date->getTimestamp() . $file->name;


    }

    public function getMediaType()
    {
        return 'contest';
    }

    public function checkFollow()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbtable('follows', 'yncontest');
        $select = $followTable->select()
            ->where('contest_id = ?', $this->contest_id)
            ->where('user_id = ?', $viewer->getIdentity());

        $row = $followTable->fetchRow($select);

        if ($row) {
            return false;
        } else {
            return true;
        }
    }

    public function checkFavourite()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'yncontest');
        $select = $favouriteTable->select()
            ->where('contest_id = ?', $this->contest_id)
            ->where('user_id = ?', $viewer->getIdentity());
        $row = $favouriteTable->fetchRow($select);
        if ($row) {
            return false;
        } else {
            return true;
        }
    }

    public function setReinvite(Core_Model_Item_Abstract $resource, User_Model_User $user)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);

        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }
        if ($row->rejected_ignored) {
            $row->rejected_ignored = false;
            $row->resource_approved = true;
            $row->user_approved = false;
            $row->save();
        }
        return $this;
    }

    public function getPrintHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'yncontest_mycontest',
            'action' => 'print-view',
            'reset' => true,
            'contestId' => $this->contest_id,
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }


    public function membership()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'yncontest'));
    }

    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    public function tags()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }


    public function getOrganizerList()
    {


        $table = Engine_Api::_()->getItemTable('yncontest_list');
        $select = $table->select()
            ->where('user_id = ?', $this->getIdentity())
            ->where('title = ?', 'yncontest_list')
            ->limit(1);

        $list = $table->fetchRow($select);

        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'user_id' => $this->getIdentity(),
                'title' => 'yncontest_list',
            ));
            $list->save();
        }

        return $list;
    }

    public function CheckOrganizers($user_id)
    {

        $table = Engine_Api::_()->getItemTable('yncontest_members');
        $select = $table->select()
            ->where('contest_id = ?', $this->getIdentity())
            ->where('member_type = 2')
            ->where('user_id =?', $user_id);

        $list = $table->fetchRow($select);
        if ($list) return true;
        return false;
    }

    public function createAction($params = array(), $page_id)
    {
        $lists = array();
        foreach ($params as $param) {

            $table = Engine_Api::_()->getItemTable('yncontest_list');
            $select = $table->select()
                ->where('user_id = ?', $page_id)
                ->where('title = ?', $param)
                ->limit(1);

            $list = $table->fetchRow($select);
            $lists[] = $list;
            if (null === $list) {
                $list = $table->createRow();
                $list->setFromArray(array(
                    'user_id' => $page_id,
                    'title' => $param,
                ));
                $list->save();
            }
        }
        return $lists;
    }


    public function approveTranByContest()
    {

        $model = new  Yncontest_Model_DbTable_Transactions();
        $trans = $model->getTranByContest($this->contest_id);
        foreach ($trans as $tran) {
            $tran->approve_status = 'approved';
            $tran->save();
        }
    }

    public function denyTranByContest()
    {
        $model = new  Yncontest_Model_DbTable_Transactions();
        $trans = $model->getTranByContest($this->contest_id);
        foreach ($trans as $tran) {
            $tran->approve_status = 'denied';
            $tran->save();
        }
    }

    public function featuredContest()
    {
        $this->featured_id = "1";
        $this->save();
    }

    public function premiumContest()
    {
        $this->premium_id = "1";
        $this->save();
    }

    public function endingContest()
    {
        $this->endingsoon_id = "1";
        $this->save();
    }

    static public function getContestCategoryName($category_id)
    {
        $category = Engine_Api::_()->getDbTable('categories', 'yncontest');
        $select = $category->select()->where('category_id = ?', $category_id);
        return $category->fetchRow($select)->name;
    }

    public function getSingletonAlbum()
    {
        $table = Engine_Api::_()->getDbTable('albums', 'yncontest');
        $select = $table->select()->where('contest_id = ?', $this->getIdentity())->order('album_id ASC')->limit(1);

        $album = $table->fetchRow($select);

        if (null === $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'title' => $this->getTitle(),
                'contest_id' => $this->getIdentity(),
                'photo_id' => $this->photo_id
            ));
            $album->save();
        }

        return $album;
    }
}

