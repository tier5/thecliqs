<?php
class Ynlistings_Model_Listing extends Core_Model_Item_Abstract {
	protected $_parent_type = 'user';
	protected $_owner_type = 'user';
	protected $_type = 'ynlistings_listing';
    
	public function getSingletonAlbum() {
        $table = Engine_Api::_() -> getItemTable('ynlistings_album');
        $select = $table -> select() -> where('listing_id = ?', $this -> getIdentity())
						-> where('title = ?', 'Listing Profile')
						 -> order('album_id ASC') -> limit(1);
        $album = $table -> fetchRow($select);

        if(null === $album) {
        	$viewer = Engine_Api::_() -> user() -> getViewer();
            $album = $table -> createRow();
            $album -> setFromArray(array('title' => 'Listing Profile', 'user_id' => $viewer->getIdentity(), 'listing_id' => $this -> getIdentity()));
            $album -> save();
        }
        return $album;
    }
	
	public function getDescription()
    {
	    if(isset($this->short_description))
	    {
	      return strip_tags($this->short_description);
	    }
	    return '';
    }
	
	public function tags()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}
	
    public function getCategory() {
        $category = Engine_Api::_()->getItem('ynlistings_category', $this->category_id);
        if ($category) {
            return $category;
        }
    }
    
    public function getCategoryTitle() {
        $category = Engine_Api::_()->getItem('ynlistings_category', $this->category_id);
        if ($category) {
            return $category->title;
        }
        else {

            return 'category can not be found.';
        }
    }
    
    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynlistings_general',
            'controller' => 'index',
            'action' => 'view',
            'id' => $this->getIdentity(),
            ), 
        $params);
        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, true);
    }
    
    public function isNew() {
        $now = new DateTime();
        $creation_date = new DateTime($this->creation_date);
        $approved_date = new DateTime($this->approved_date);
        $new_days = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynlistings_new_days', 3);
        if ($approved_date) {
            $diff = date_diff($approved_date, $now);
        }
        else $diff = date_diff($creation_date, $now);
        $measure = ($diff->format('%a'));
        if ($measure <= $new_days) return true;
        return false;
    }
	
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Ynlistings_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'listing',
			'parent_id' => $this -> getIdentity()
		);
		// Save
		$storage = Engine_Api::_ ()->storage ();

		// Resize image (main)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 720, 720 )->write ( $path . '/m_' . $name )->destroy ();

		// Resize image (profile)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 300, 300 )->write ( $path . '/p_' . $name )->destroy ();

		// Resize image (icon)
		$image = Engine_Image::factory ();
		$image->open ( $file );

		$size = min ( $image->height, $image->width );
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample ( $x, $y, $size, $size, 50, 50 )->write ( $path . '/is_' . $name )->destroy ();

		// Store
		$iMain = $storage->create ( $path . '/m_' . $name, $params );
		$iProfile = $storage->create ( $path . '/p_' . $name, $params );
		$iSquare = $storage->create ( $path . '/is_' . $name, $params );

		$iMain->bridge ( $iProfile, 'thumb.profile' );
		$iMain->bridge ( $iSquare, 'thumb.icon' );

		// Remove temp files
		@unlink ( $path . '/p_' . $name );
		@unlink ( $path . '/m_' . $name );
		@unlink ( $path . '/is_' . $name );
		// Update row
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();
		//add photo to profile album listing
		$album = $this->getSingletonAlbum();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photo = Engine_Api::_()->getItemTable("ynlistings_photo") -> createRow();
		$photo->collection_id = $album->album_id;
		$photo->listing_id = $this->getIdentity();
		$photo->user_id = $viewer->getIdentity();
        $photo->album_id = $album->album_id;
		$photo->file_id = $iMain -> getIdentity();
        $photo->save();
		return $this;
	}
    
    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
    
    function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit'); 
    }
    
    function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete'); 
    }

    function canUploadPhotos() {
        return $this->authorization()->isAllowed(null, 'upload_photos'); 
    }
    
    function canUploadVideos() {
        return $this->authorization()->isAllowed(null, 'upload_videos'); 
    }
    
    function canDiscuss() {
        return $this->authorization()->isAllowed(null, 'discussion'); 
    }
    
    function canShare() {
        return $this->authorization()->isAllowed(null, 'share'); 
    }
    
    function canPrint() {
        return $this->authorization()->isAllowed(null, 'print'); 
    }
    
    function canLike() {
        return $this->authorization()->isAllowed(null, 'comment'); 
    }
    
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
    }
    
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
    }
    
    public function isOverMax() {
        $owner = $this->getOwner();
        $table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
        $select = $table->select()->where('user_id = ?', $owner->getIdentity());
        $count_listings = count($table->fetchAll($select));
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_listings_auth = $permissionsTable->getAllowed('ynlistings_listing', $owner->level_id, 'max_listings');
        if ($max_listings_auth == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $owner->level_id)
                ->where('type = ?', 'ynlistings_listing')
                ->where('name = ?', 'max_listings'));
            if ($row) {
                $max_listings_auth = $row->value;
            }
        }
        if ($max_listings_auth > 0 && $count_listings > $max_listings_auth) return true;
        return false;
    }
    
    public function expired() {
        if (!$this->end_date) return false;
        $now = new DateTime();
        $end_date = new DateTime($this->end_date);
        if ($now > $end_date) return true;
        return false;
    }
    
    public function ratingCount() {
        $table = Engine_Api::_()->getItemTable('ynlistings_review');
        $select = $table->select()
            ->where('listing_id = ?', $this->getIdentity())
            ->where('user_id <> ?', $this->user_id);
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }
    
    public function getRating() {
        $table = Engine_Api::_()->getItemTable('ynlistings_review');
        $rating_sum = $table->select()
            ->from($table->info('name'), new Zend_Db_Expr('SUM(rate_number)'))
            ->group('listing_id')
            ->where('listing_id = ?', $this->getIdentity())
            ->where('user_id <> ?', $this->user_id)
            ->query()
            ->fetchColumn(0)
        ;

        $total = $this->ratingCount();
        if ($total)
            $rating = $rating_sum / $total;
        else
            $rating = 0;

        return $rating;
    }
    
    public function checkRated() {
        $table = Engine_Api::_()->getItemTable('ynlistings_review');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $rName = $table->info('name');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->where('listing_id = ?', $this->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity())
            ->limit(1);
        $row = $table->fetchAll($select);

        if (count($row) > 0)
            return true;
        return false;
    }

    public function sendEmailToFriends($recipients, $message) {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Check recipients
        if( is_string($recipients) ) {
          $recipients = preg_split("/[\s,]+/", $recipients);
        }
        if( is_array($recipients) ) {
          $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }
        if( !is_array($recipients) || empty($recipients) ) {
          return 0;
        }
    
        // Check message
        $message = trim($message);
        $sentEmails = 0;
        $photo_url = ($this->getPhotoUrl('thumb.profile')) ? $this->getPhotoUrl('thumb.profile') : 'application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png';
        foreach( $recipients as $recipient ) {
            $mailType = 'ynlistings_email_to_friends';
            $mailParams = array(
              'host' => $_SERVER['HTTP_HOST'],
              'email' => $recipient,
              'date' => time(),
              'sender_email' => $viewer->email,
              'sender_title' => $viewer->getTitle(),
              'sender_link' => $viewer->getHref(),
              'sender_photo' => $viewer->getPhotoUrl('thumb.icon'),
              'message' => $message,
              'object_link' => $this->getHref(),
              'object_title' => $this->title,
              'object_photo' => $photo_url,
              'object_description' => $this->description, 
            );
            
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $recipient,
              $mailType,
              $mailParams
            );
            $sentEmails++;
         }
        return $sentEmails;
    }

    public function getMediaType() {
        return 'listing';
    }
	
	protected function _delete()
	{

		// Delete all albums
		$albumTable = Engine_Api::_() -> getItemTable('ynlistings_album');
		$albumSelect = $albumTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($albumTable->fetchAll($albumSelect) as $listingAlbum)
		{
			$listingAlbum -> delete();
		}

		// Delete all topics
		$topicTable = Engine_Api::_() -> getItemTable('ynlistings_topic');
		$topicSelect = $topicTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($topicTable->fetchAll($topicSelect) as $listingTopic)
		{
			$listingTopic -> delete();
		}

		//Delete all announcment
		$reviewTable = Engine_Api::_() -> getItemTable('ynlistings_review');
		$reviewSelect = $reviewTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($reviewTable->fetchAll($reviewSelect) as $listingReview)
		{
			$listingReview -> delete();
		}

		//Delete reports
		$reportTable = Engine_Api::_() -> getItemTable('ynlistings_report');
		$reportSelect = $reportTable -> select() -> where('listing_id = ?', $this -> getIdentity());
		foreach ($reportTable->fetchAll($reportSelect) as $listingReport)
		{
			$listingReport -> delete();
		}

		parent::_delete();
	}
	
}