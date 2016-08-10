<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Images.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Model_DbTable_Images extends Engine_Db_Table {

    protected $_name = 'spectacular_images';
    protected $_rowClass = "Spectacular_Model_Image";

    public function getImages($params = array(), $columns = array()) {
        $tableName = $this->info('name');
        $select = $this->select();

        if (!empty($columns))
            $select->from($tableName, $columns);

        if (isset($params['enabled'])) {
            $select->where('enabled = ?', $params['enabled']);
        }

        if (isset($params['selectedImages'])) {
            $select->where('image_id' . ' in (?)', new Zend_Db_Expr(trim(implode(',', $params['selectedImages']), ',')));
        }

        $select->order("order ASC");

        return $this->fetchAll($select);
    }

    public function getTitleMatch($title) {
        $tableName = $this->info('name');
        $select = $this->select();
        $title = $select->from($tableName, 'title')->where('title = ?', $title)->query()->fetchColumn();

        if ($title) {
            return $title;
        }

        return false;
    }

}
