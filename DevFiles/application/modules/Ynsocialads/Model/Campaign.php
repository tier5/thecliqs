<?php
class Ynsocialads_Model_Campaign extends Core_Model_Item_Abstract {
    function isViewable()  { return $this->authorization()->isAllowed(null, 'view'); }
    function isEditable()  { return $this->authorization()->isAllowed(null, 'edit'); }
    function isDeletable() { return $this->authorization()->isAllowed(null, 'delete'); }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function deleteAllAds() {
        $adsTbl = Engine_Api::_()->getItemTable('ynsocialads_ad');
        $ads = $adsTbl->fetchAll($adsTbl->select()->where('campaign_id = ?', $this->getIdentity()));
        foreach ($ads as $ad) {
            $ad->deleted = true;
            $ad->status = 'deleted';
            $ad->save();
        }
    }
    
    public function countDetail() {
        $adsTbl = Engine_Api::_()->getItemTable('ynsocialads_ad');
        $adsTblName = $adsTbl->info('name');
        $select = $adsTbl->select()
        ->from($adsTblName, array(
        'ads' => 'COUNT(`ad_id`)',
        'clicks' => 'SUM(`click_count`)',
        'impressions' => 'SUM(`impressions_count`)',
        ))
        ->where('campaign_id = ?', $this->getIdentity());
        
        $result = $adsTbl->fetchRow($select);
        return (($result) ? $result->toArray() : array());
    }
}