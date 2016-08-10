<?php

class Ynfbpp_Model_DbTable_Popup extends Engine_Db_Table {

    protected $_name = 'ynfbpp_popup';

    public function popupSetting($values) {
        
        $profileType = isset($values["profileType"])?$values["profileType"]:'';
        
        if (isset($values["limit"])) {
            $limit = $values["limit"];
            Engine_Api::_()->getApi('settings', 'core')->setSetting('ynfbpp.profile.' . $profileType, $limit);
        }
        
        if(isset($values["field"]) && !empty($values["field"])){
            foreach ($values["field"] as $id => $field) {
                $select = $this->select()->where("field_id = ?", $id);
                $row = $this->fetchRow($select);
                if (!$row) {
                    $row = $this->createRow();
                    $row->field_id = $id;
                }
                $row->enabled = 0;
                if (isset($values["enabled"][$id])) {
                    $row->enabled = 1;
                }
                if (isset($values["ordering"][$id])) {
                    $row->ordering = $values["ordering"][$id];
                }
    //            var_dump($row);
    //            die;
                $row->save();
            }
        }
    }

    public function getField($id) {
        $select = $this->select()->where("field_id = ?", $id);
        return $this->fetchRow($select);
    }

    public function getFieldsByOption($option_id, $fieldValues) {
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('user');
        // Get second level fields
        $secondLevelMaps = array();
        $secondLevelFields = array();
        if (!empty($option_id)) {
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if (!empty($secondLevelMaps)) {
                foreach ($secondLevelMaps as $map) {
                    $secondLevelFields[$map->child_id] = $map->getChild();
                }
            }
        }
        $fieldsArray = array();
        $fieldsArray = array(0);
        foreach ($secondLevelFields as $field) {
            $fieldsArray[] = $field->field_id;
        }

        $select = $this->select()->where("enabled =?", 1)
                ->where("field_id in (?)", $fieldsArray)
                ->order("ordering asc");
                
        
        $popupFields = $this->fetchAll($select);
        $result = array();
        foreach ($popupFields as $popup) {
            $id = $popup['field_id'];
            foreach ($fieldValues as $value) {
                if ($value['field_id'] == $id) {
                    $valueString = $value['value'];
                    foreach ($secondLevelFields as $field) {
                        if ($field['field_id'] == $id)
                            $label = $field['label'];
                    }
                    $result[$id]['label'] = $label;
                    $result[$id]['value'] = $valueString;
                }
            }
        }
        // echo $select; die;
        return $result;
        //return $this->fetchAll($select);
    }

}