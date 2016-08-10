<?php
class Ynresume_Model_DbTable_Photos extends Engine_Db_Table {
    protected $_rowClass = 'Ynresume_Model_Photo';
    
    public function resetPhotoItem($resume, $item) {
        $data = array (
            'parent_type' => $resume->getType(),
            'parent_id' => $resume->getIdentity(),
        );
        $where = array();
        $where = $this->getAdapter()->quoteInto('parent_type = ?', $item->getType());
        $where = $this->getAdapter()->quoteInto('parent_id = ?', $item->getIdentity());
        $this->update($data, $where);
    }
    
    public function updatePhotoParent($ids , $item) {
        $data = array (
            'parent_type' => $item->getType(),
            'parent_id' => $item->getIdentity(),
        );
        $where = $this->getAdapter()->quoteInto('photo_id IN (?)', $ids);
        if (count($ids) > 0) {
            $this->update($data, $where);
        }
    }
    
    public function getPhotosItem($item) {
        $select = $this->select()->where('parent_type = ?', $item->getType())->where('parent_id = ?', $item->getIdentity());
        return $this->fetchAll($select);
    }
    
    public function getPhotoIdsItem($item) {
        $data = $this->getPhotosItem($item);
        $ids = array();
        foreach ($data as $row) {
            $ids[] = $row->photo_id;
        }
        return $ids;
    }
}
