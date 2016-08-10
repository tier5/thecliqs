<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-08-16 16:16 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Daylogo_Api_Core extends Core_Api_Abstract
{
  public function getLogoSelect()
  {
    $table = Engine_Api::_()->getDbTable('logos', 'daylogo');
    $select = $table->select()
      ->order('start_date ASC');
    return $select;
  }

  public function getLogoPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getLogoSelect());
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return ($paginator);
  }

  public function uploadPhoto($photo, $params = array())
  {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      throw new Exception('Invalid argument passed to uploadLogo: ' . print_r($photo, 1));
    }

    $extension = ltrim(strrchr($photo['name'], '.'), '.');
    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';


    $params = array_merge(array(
      'name' => $name,
      'parent_type' => 'daylogo_logo',
      'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'extension' => $extension,
    ), $params);

    $storage = Engine_Api::_()->storage();

    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(174, 174)
      ->write($path . '/t_' . $name . '.' . $extension)
      ->destroy();

    $image = Engine_Image::factory();
    $image->open($file)
      ->write($path . '/o_' . $name . '.' . $extension)
      ->destroy();

    $iTmp = $storage->create($path . '/t_' . $name . '.' . $extension, $params);
    $iOriginal = $storage->create($path . '/o_' . $name . '.' . $extension, $params);

    $iTmp->bridge($iTmp, 'thumb.view');
    $iTmp->bridge($iOriginal, 'thumb.original');

    @unlink($path . '/t_' . $name . '.' . $extension);
    @unlink($path . '/o_' . $name . '.' . $extension);

    return $iTmp;
  }

  public function deletePhoto($logo_id, $original_img = false)
  {
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR;
    $storage = Engine_Api::_()->storage();
    $logo = $storage->get($logo_id);
    if( is_object($logo) ) {
      if ($logo->type == 'thumb.view') {
        $logoPath = $path . $logo->storage_path;
        $logo->delete();
        $last_dir = dirname($logoPath);
        $parent_dir_pos = strrpos($last_dir, '/');
        $parent_dir = substr($last_dir, 0, $parent_dir_pos);
        @unlink($logoPath);
        @rmdir($last_dir);
        @rmdir($parent_dir);
      }

      if ($original_img === false) {
        $thumb = ($logo->type == 'thumb.original') ? $logo : $storage->get($logo_id, 'thumb.original');
        if ($thumb) {
          $normalImg = $storage->get($thumb->getIdentity(), 'thumb.normal');
          $logoPath = $path . $thumb->storage_path;
          $thumb->delete();
          $last_dir = dirname($logoPath);
          $parent_dir_pos = strrpos($last_dir, '/');
          $parent_dir = substr($last_dir, 0, $parent_dir_pos);
          @unlink($logoPath);
          @rmdir($last_dir);
          @rmdir($parent_dir);
          if ($normalImg) {
            $normalPath = $path . $normalImg->storage_path;
            $normalImg->delete();
            $normal_last_dir = dirname($normalPath);
            $n_parent_dir_pos = strrpos($normal_last_dir, '/');
            $n_parent_dir = substr($normal_last_dir, 0, $n_parent_dir_pos);
            @unlink($normalPath);
            @rmdir($normal_last_dir);
            @rmdir($n_parent_dir);
          }
        }
      }
    }
  }

}