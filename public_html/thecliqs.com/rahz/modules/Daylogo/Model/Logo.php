<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Logo.php 2012-08-16 16:35 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Daylogo_Model_Logo extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';
  protected $_parent_is_owner = true;

  public function setLogo($img_id)
  {
    $file = Engine_Api::_()->storage()->get($img_id, 'thumb.original');

    $extension = ltrim(strrchr($file->name, '.'), '.');
    $base = rtrim(substr(basename($file->name), 0, strrpos(basename($file->name), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    $paramsUpdate = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity()
    );

    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'name' => $file->name,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    //Save displayed logo
    $normalWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('daylogo.maxwidth');
    $normalHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('daylogo.maxheight');

    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file->storage_path)
      ->resize($normalWidth, $normalHeight)
      ->write($normalPath)
      ->destroy();

    // Store
    try {
      $iLogoNormal = $filesTable->createFile($normalPath, $params);

      $logoTable = Engine_Api::_()->getDbTable('logos', 'daylogo');
      $logoTable->update(array(
        'photo_id' => $img_id,
      ), array(
        'logo_id = ?' => $this->getIdentity(),
      ));
      $file->bridge($iLogoNormal, 'thumb.normal');
      $contentTable = Engine_Api::_()->getDbTable('content', 'core');
      //If this logo is active
      $daylogo = $contentTable->select()
        ->where('name = ?', 'daylogo.day-logo')
        ->query()
        ->fetch();
      if (is_array($daylogo)) {
        $params = Zend_Json::decode($daylogo['params']);
        if ($params['logo_id'] === $this->getIdentity()) {
          $logoTable->deactivateLogo($params);
        }
      }
      try {
        $file->setFromArray($paramsUpdate);
        $file->save();
      } catch (Exception $e) {
        return;
      }

    } catch (Exception $e) {
      // Remove temp files
      @unlink($file->storage_path);
      @unlink($normalPath);
    }
    // Remove temp files
    @unlink($normalPath);

    // Update row
    $this->save();

    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }

    return $this;
  }

  public function getHref($params = array())
  {
    $slug = $this->getSlug();

    $params = array_merge(array(
      'route' => 'daylogo_manage',
      'reset' => true,
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'logo_id' => $this->logo_id,
      //'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getPhotoUrl($type = null)
  {
    $photo_id = $this->photo_id;
    if (!$photo_id) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
    if (!$file) {
      return null;
    }

    return $file->map();
  }

  public function getPhotoSize($type = null, $widthHeight = 1)
  {
    $photo_id = $this->photo_id;
    if (!$photo_id) {
      return null;
    }
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
    if (!$file) {
      return null;
    }
    $size = getimagesize($file->storage_path);
    //width
    if ($widthHeight === 1) {
      return $size[0];
    }
    if ($widthHeight === 2) {
      return $size[1];
    }

  }

}