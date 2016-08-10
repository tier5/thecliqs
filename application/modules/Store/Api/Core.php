<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Api_Core extends Core_Api_Abstract
{
  const IMAGE_WIDTH = 900;
  const IMAGE_HEIGHT = 900;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;

  /**
   * @var Page_Model_Page
   */
	protected $_store;

	/**
   * @var Store_Model_Api
   */
  protected $_api;

	/**
	 * @return Boolean
	 **/
  public function isActiveTransaction()
  {
    $session = new Zend_Session_Namespace('Store_Transaction');

    if (!$session->order_id || !$session->cart_id) {
      return false;
    }

    if (null == $order = Engine_Api::_()->getItem('payment_order', $session->order_id)) {
      return false;
    }

    if ($order->source_type != 'store_cart' || !in_array($order->state, array('initial', 'pending')) || $session->cart_id != $order->source_id) {
      return false;
    }

    if (null == ($cart = Engine_Api::_()->getItem($order->source_type, $order->source_id))) {
      return false;
    }

    if (!in_array($cart->status, array('initial', 'pending'))) {
      return false;
    }

    return true;
  }

  public function createPhoto($params, $file)
  {
    if( $file instanceof Storage_Model_File ) {
      $params['file_id'] = $file->getIdentity();

    } else {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName  = $path.'/m_'.$name . '.' . $extension;
      $thumbName = $path.'/t_'.$name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
          ->write($mainName)
          ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
          ->write($thumbName)
          ->destroy();

      // Store photos
      $photo_params = array(
        'parent_id' => $params['owner_id'],
        'parent_type' => 'user',
      );

      try {
        $photoFile = Engine_Api::_()->storage()->create($mainName,  $photo_params);
        $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
      } catch (Exception $e) {
        if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE)
        {
          echo $e->getMessage();
          exit();
        }
      }

      $photoFile->bridge($thumbFile, 'thumb.normal');

      // Remove temp files
      @unlink($mainName);
      @unlink($thumbName);

      $params['file_id']  = $photoFile->file_id; // This might be wrong
      $params['photo_id'] = $photoFile->file_id;
      $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    }

    $row = $this->getPhotoTable()->createRow();
    $row->setFromArray($params);
    $row->save();
    return $row;
  }

  public function getPhotoTable()
  {
    return Engine_Api::_()->getDbTable('photos', 'store');
  }

  public function isApiEnabled($params = array('title' => 'PayPal'))
  {
    if (!($gateway = $this->getApi($params))) {
      return false;
    }

    return $gateway->enabled;
  }

  /**
   * @param array $params
   * @return bool|null|Store_Model_Api
   */
  public function getApi($params = array('title' => 'PayPal'))
  {
    if ($this->_api instanceof Store_Model_Api) {
      return $this->_api;
    }

    $store = $this->getStore();

    //he@todo exception
    if (!($store instanceof Page_Model_Page)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('gateways', 'payment');
    $select = $table->select()->where('title=?', $params['title']);

    /**
     * @var $gateway Payment_Model_Gateway
     */
    if (null == ($gateway = $table->fetchRow($select))) {
      return false;
    }

    /**
     * @var $apiTable Store_Model_DbTable_Apis
     */
    $apiTable = Engine_Api::_()->getDbTable('apis', 'store');

    $select = $apiTable->select()
      ->where('gateway_id=? ', $gateway->gateway_id)
      ->where('page_id=?', $store->getIdentity());

    if (null == ($api = $apiTable->fetchRow($select))) {
      $api = $apiTable->createRow(array(
        'gateway_id' => $gateway->gateway_id,
        'page_id' => $store->getIdentity()
      ));
      $api->save();
    }

    $this->_api = $api;

    return $api;
  }

  /**
   * @param null|Store_Model_Api $api
   * @return Store_Plugin_Gateway_PayPal
   */
  public function getPlugin(Store_Model_Api $api = null)
  {
    if ($api == null) {
      $api = $this->getApi();
    }

    if (!($api instanceof Store_Model_Api)) {
      return null;
    }

    return $api->getPlugin();
  }

  public function setStore(Page_Model_Page $store)
  {
    $this->_store = $store;
  }

	/**
	 * @return Page_Model_Page
	 */
	public function getStore()
	{
		return $this->_store;
	}

  public function createThumbnail($video)
  {
    // Now try to create thumbnail
    $thumbnail = $this->handleThumbnail($video->type, $video->code);
    $ext = ltrim(strrchr($thumbnail, '.'), '.');
    $thumbnail_parsed = @parse_url($thumbnail);

    if (@GetImageSize($thumbnail)){
      $valid_thumb = true;
    } else {
      $valid_thumb = false;
    }

    if( $valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png')) ){
      $tmp_file = APPLICATION_PATH . '/temporary/link_'.md5($thumbnail).'.'.$ext;
      $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_'.md5($thumbnail).'.'.$ext;
      $mini_file = APPLICATION_PATH . '/temporary/link_mini_'.md5($thumbnail).'.'.$ext;
      $icon_file = APPLICATION_PATH . '/temporary/link_thumb_icon_'.md5($thumbnail).'.'.$ext;

      $src_fh = fopen($thumbnail, 'r');
      $tmp_fh = fopen($tmp_file, 'w');
      stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

      $image = Engine_Image::factory();
      $image->open($tmp_file)
        ->resize(240, 180)
        ->write($thumb_file)
        ->destroy();

      $image = Engine_Image::factory();
      $image->open($tmp_file)
        ->resize(34, 34)
        ->write($mini_file)
        ->destroy();

      $image = Engine_Image::factory();
      $image->open($tmp_file)
        ->resize(48, 48)
        ->write($icon_file)
        ->destroy();

      try {
        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
          'parent_type' => $video->getType(),
          'parent_id' => $video->getIdentity()
        ));

        $thumbMiniFileRow = Engine_Api::_()->storage()->create($mini_file, array(
          'parent_type' => $video->getType(),
          'parent_id' => $video->getIdentity()
        ));

        $thumbIconFileRow = Engine_Api::_()->storage()->create($icon_file, array(
          'parent_type' => $video->getType(),
          'parent_id' => $video->getIdentity()
        ));

        $thumbFileRow->bridge($thumbMiniFileRow, 'thumb.mini');
        $thumbFileRow->bridge($thumbIconFileRow, 'thumb.icon');

        // Remove temp file
        @unlink($thumb_file);
        @unlink($mini_file);
        @unlink($tmp_file);
        @unlink($icon_file);
      }
      catch (Exception $e)
      {
        throw $e;
      }
      $information = $this->handleInformation($video->type, $video->code);

      $video->duration = $information['duration'];
      if (!$video->description) $video->description = $information['description'];
      $video->photo_id = $thumbFileRow->file_id;
      $video->status = 1;
      $video->save();
    }
  }

      // handles thumbnails
  public function handleThumbnail($type, $code = null)
  {
    switch ($type) {
      //youtube
      case "1":
        // http://img.youtube.com/vi/E98IYokujSY/default.jpg
        return "http://img.youtube.com/vi/$code/0.jpg";
      // vimeo
      case "2":
        // thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/".$code.".xml");
        $thumbnail = $data->video->thumbnail_large;
        return $thumbnail;
    }
  }

  // retrieves infromation and returns title + desc
  public function handleInformation($type, $code)
  {
    switch ($type) {
      //youtube
      case "1":
        $yt = new Zend_Gdata_YouTube();
        $youtube_video = $yt->getVideoEntry($code);
        $information = array();
        $information['title'] = $youtube_video->getTitle();
        $information['description'] = $youtube_video->getVideoDescription();
        $information['duration'] = $youtube_video->getVideoDuration();

        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/".$code.".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] =  $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;

        return $information;
    }
  }

  public function deleteVideo($video)
  {
    if ($video->status == 1) {
      if ($video->photo_id) Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
    }

    if(null != ($row = Engine_Api::_()->getDbTable('videos', 'store')->findRow($video->video_id))){
      $row->delete();
    }
  }

  public function deleteAudio($audio)
  {
    if ($audio->file_id){
      Engine_Api::_()->getItem('storage_file', $audio->file_id)->remove();
    }

    if(null != ($row = Engine_Api::_()->getDbTable('audios', 'store')->findRow($audio->audio_id))){
      $row->delete();
    }

  }

  public function deleteFile($file_id)
  {
    if ($file_id && (null != ($file = Engine_Api::_()->getItem('storage_file', $file_id))))
    {
      $file->remove();
    }

    return;
  }

// handle audio upload
  public function createAudio($file, $params = array())
  {
    if( is_array($file) ) {
      if( !is_uploaded_file($file['tmp_name']) ) {
        throw new Storage_Model_Exception('Invalid upload or file too large');
      }
      $filename = $file['name'];
    } else if( is_string($file) ) {
      $filename = $file;
    } else {
      throw new Storage_Model_Exception('Invalid upload or file too large');
    }

    // Check file extension
    if( !preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $filename) ) {
      throw new Storage_Model_Exception('Invalid file type');
    }

    $storage = Engine_Api::_()->getItemTable('storage_file');

    $row = $storage->createRow();
    $row->setFromArray(array(
      'parent_type' => 'store_audio',
      'parent_id' => 1, // Hack
      'user_id' => null,
    ));

    $row->store($file);
    return $row;
  }

  public function createFile($file)
  {
    if( is_array($file) ) {
      if( !is_uploaded_file($file['tmp_name']) ) {
        throw new Storage_Model_Exception('Invalid upload or file too large');
      }
      $filename = $file['name'];
    } else if( is_string($file) ) {
      $filename = $file;
    } else {
      throw new Storage_Model_Exception('Invalid upload or file too large');
    }

    $htaccess = APPLICATION_PATH . '/public/store_product/.htaccess';

    if (!file_exists($htaccess)) {
      $fp = fopen($htaccess, "w");
      fwrite($fp, "deny from all");
      fclose($fp);
    }

    $storage = Engine_Api::_()->getItemTable('storage_file');

    $row = $storage->createRow();
    $row->setFromArray(array(
      'parent_type' => 'store_product',
      'parent_id' => 1, // Hack
      'user_id' => null,
    ));

    $row->store($file);
    return $row;
  }

  public function readfile_chunked($filename, $retbytes = true)
  {
    $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
    $cnt = 0;
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
      return false;
    }
    ob_end_clean(); //added to fix ZIP file corruption
    ob_start(); //added to fix ZIP file corruption

    while (!feof($handle)) {
      $buffer = fread($handle, $chunksize);
      echo $buffer;
      ob_flush();
      flush();
      if ($retbytes) {
        $cnt += strlen($buffer);
      }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
      return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
  }

  public function generate_random_letters($length)
  {
    $random = '';
    for ($i = 0; $i < $length; $i++) {
      $random .= chr(rand(ord('a'), ord('z')));
    }
    return $random;
  }

  function params_string($haystack, $delimiter1 = ': ', $delimiter2 = ', ')
  {
    if( !is_array( $haystack ) ) return false;

    $array_str = '';
    $i = 0;
    foreach( $haystack as $arr )
    {
      if( $i > 0 ) $array_str .= $delimiter2;
      $array_str .= $arr['label'] . $delimiter1 . $arr['value'];
      $i++;
    }

    return $array_str;
  }

  public function getCommission($amt)
  {
    $amt = (double)$amt;
    $commission = 0.00;

    if ($amt <= 0.00 || $this->getPaymentMode() == 'client_store') {
      return $commission;
    }

    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $commissionFixed = (double)$settings->getSetting('store.commission.fixed', 0);
    $commissionPercentage = (int)$settings->getSetting('store.commission.percentage', 0);
    $commission = round((double)($commissionFixed + ($amt * $commissionPercentage)/100), 2);

    return $commission;
  }

  public function isStoreCreditEnabled()
  {
    if (!$this->isCreditEnabled()) {
      return false;
    }

    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    if (!$isPageEnabled) {
      return false;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $isStoreEnabled = $settings->getSetting('store.credit.store', 0);
    if (!$isStoreEnabled) {
      return false;
    }

    return true;
  }

  public function isCreditEnabled()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $switcher = $settings->getSetting('store.credit.enabled', 0);
    $isModuleEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit');
    if (!$isModuleEnabled || !$switcher) {
      return false;
    }

    return true;
  }

  public function getCredits($price)
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('credit.default.price', 100);

    return (int)ceil($price * $defaultPrice);
  }

  public function getPaymentMode()
  {
    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    if (!$isPageEnabled) {
      return 'client_site_store';
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $mode = $settings->getSetting('store.payment.mode', 'client_site_store');
    return $mode;
  }
}