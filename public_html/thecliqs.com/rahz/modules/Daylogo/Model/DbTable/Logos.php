<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Logos.php 2012-08-16 16:36 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Daylogo_Model_DbTable_Logos extends Engine_Db_Table
{
  protected $_rowClass = 'Daylogo_Model_Logo';

  public function checkDaylogo($start_date, $end_date, $params)
  {
    $oldTz = date_default_timezone_get();
    $sitetz = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone');
    date_default_timezone_set($sitetz);
    $datetime = date('Y-m-d H:i:s');
    date_default_timezone_set($oldTz);
    if ($end_date <= $datetime or $start_date > $datetime and $end_date > $datetime) {
      return $this->getDaylogo($params);
    }
    if ($end_date > $datetime and $start_date < $datetime) {
      //If there are other logos in this period
      if( $this->countDaylogo(strtotime($datetime)) > 1 ){
        $prev_start_date = 0;
        foreach( $this->getLogosByDate($datetime) as $logo ){
          if( $logo['start_date'] > $prev_start_date ){
            $prev_start_date = $logo['start_date'];
            $activeLogo = $logo;
          }
        }
        if( $activeLogo['logo_id'] != $params['logo_id'] ){
          return $this->getDaylogo($params);
        }
      }
      //if disabled logo
      $logo = $this->select()
        ->where('logo_id = ?', $params['logo_id'])
        ->query()
        ->fetch();
      if($logo['enabled'] == 0){
        return $this->getDaylogo($params);
      }else{
        return $params['logo_id'];
      }
    }
  }

  public function getDaylogo($params, $daylogo = false, $logo_date = null)
  {
    $oldTz = date_default_timezone_get();
    if( Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ) {
      date_default_timezone_set(Engine_Api::_()->user()->getViewer()->timezone);
    }
    $date_str = ($logo_date === null) ? date("Y-m-d H:i:s") : $logo_date;
    date_default_timezone_set($oldTz);
    if ($date_str === -1) {
      return;
    }

    $count_daylogo = $this->countDaylogo(strtotime($date_str));
    if ($count_daylogo > 0) {
      return $daylogo === true ? true : $this->activateLogo($params, $this->getLogosByDate($date_str));
    }
    if ($count_daylogo === 0) {
      if ($daylogo === true) {
        return false;
      }
      if (!empty($params['default'])) {
        return $this->activateLogo($params, $params['default'], true);
      }
      if (empty($params['default'])) {
        $this->deactivateLogo($params);
        return false;
      }
    }
  }

  public function countDaylogo($date)
  {
    $logo = $this->select()->from($this->info('name'), array('count' => new Zend_Db_Expr('COUNT(*)')))
      ->where('start_date <= ? AND end_date > ? AND enabled = 1', date("Y-m-d H:i:s", $date))
      ->query()
      ->fetch();

    return $logo['count'];
  }

  public function checkStartTime($start_date)
  {
    $oldTz = date_default_timezone_get();
    date_default_timezone_set(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone'));
    $logo = $this->select()->from($this->info('name'), array('count' => new Zend_Db_Expr('COUNT(*)')))
      ->where('start_date = ?', date("Y-m-d H:i:s", $start_date))
      ->query()
      ->fetch();
    date_default_timezone_set($oldTz);

    return $logo['count'];
  }

  public function activateLogo($params, $logos = false, $default = false)
  {
    $contentTable = Engine_Api::_()->getDbTable('content', 'core');
    $coreLogo = $contentTable->fetchRow($contentTable->select()
      ->where('name = ?', 'daylogo.day-logo'));
    if( !$coreLogo ) {
      return false;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    if($default === true) {
      try {
        if( is_numeric($params['logo_id']) and $params['logo_id'] != 0 ) {
          $this->update(array('active' => 0), array('logo_id = ?' => $params['logo_id']));
        }
        $params['logo'] = $logos;
        $params['logo_id'] = 0;
        $values = array(
          'params' => Zend_Json::encode($params)
        );
        $coreLogo->setFromArray($values);
        $coreLogo->save();
        $db->commit();
      } catch(Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    if($default === false) {
      $prev_start_date = 0;
      foreach($logos as $logo) {
        if($logo['start_date'] > $prev_start_date){
          $prev_start_date = $logo['start_date'];
          $activeLogo = $logo;
        }
      }
      $photoTable = Engine_Api::_()->getDbTable('files', 'storage');
      $photoOriginal = $photoTable->getFile($activeLogo['photo_id'], 'thumb.original');
      $photoNormal = $photoTable->getFile($photoOriginal->file_id, 'thumb.normal');

      $photoPath = $photoNormal->storage_path;
      try {
        $params['logo'] = $photoPath;
        $params['logo_id'] = $activeLogo['logo_id'];
        $values = array(
          'params' => Zend_Json::encode($params)
        );
        $coreLogo->setFromArray($values);
        $coreLogo->save();

        $this->update(array('active' => 1), array('logo_id = ?' => $activeLogo['logo_id'] ));
        $this->update(array('active' => 0), array('logo_id != ? AND active = 1' => $activeLogo['logo_id']));

        $db->commit();

        return $activeLogo['logo_id'];
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function getLogoPath($name)
  {
    $table = Engine_Api::_()->getDbTable('content', 'core');
    $logo = $table->select()
      ->where('name = ?', $name)
      ->query()
      ->fetch();
    if( !$logo ) {
      return false;
    }
    $params = Zend_Json::decode($logo['params']);

    return $params['logo'];
  }

  public function deactivateLogo($params)
  {
    $contentTable = Engine_Api::_()->getDbTable('content', 'core');
    $coreLogo = $contentTable->fetchRow($contentTable->select()
      ->where('name = ?', 'daylogo.day-logo'));

    if( !$coreLogo ) {
      return false;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      if( is_numeric($params['logo_id']) and $params['logo_id'] != 0 ) {
        $this->update(array('active' => 0), array('logo_id = ?' => $params['logo_id']));
      }
      $params['logo'] = '';
      $params['logo_id'] = 0;

      $values = array(
        'params' => Zend_Json::encode($params)
      );
      $coreLogo->setFromArray($values);
      $coreLogo->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function getPreviewLogo($photo_id)
  {
    $storage = Engine_Api::_()->storage();
    $logoOriginal = $storage->get($photo_id, 'thumb.original');
    if ($logoOriginal) {
      $logo = $storage->get($logoOriginal->getIdentity(), 'thumb.normal');
      return $logo->storage_path;
    } else {
      return false;
    }

  }

  public function getLogo($logo_id, $id_type = 'photo')
  {
    try {
    $logo = $this->select()
      ->where($id_type == 'photo' ? 'photo_id = ?' : 'logo_id = ?', $logo_id)
      ->query()
      ->fetch();
    } catch(Exception $e) {
      throw $e;
    }
    return $logo;
  }

  public function getLogosByDate($date)
  {
    $logos = $this->select()
      ->where("start_date <= ? AND end_date >= ? AND enabled = 1", $date)
      ->query()
      ->fetchAll();
    return $logos;
  }

  public function getLogoParams($name)
  {
    $contentTable = Engine_Api::_()->getDbTable('content', 'core');
    $coreLogo = $contentTable->select()
      ->where('name = ?', $name)
      ->query()
      ->fetch();
    return Zend_Json::decode($coreLogo['params']);
  }
}