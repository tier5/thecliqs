<?php
class Ynresume_Model_DbTable_Recommendations extends Engine_Db_Table {
    protected $_rowClass = 'Ynresume_Model_Recommendation';
    
    public function getReceivedRecommendations($user_id) {
        $select = $this->select()->where('receiver_id = ?', $user_id)
        ->where('status = ?', 'given')
        ->where('`show` = ?', 1)
        ->order('given_date DESC');
        return $this->fetchAll($select);
    }
    
    public function getGivenRecommendations($user_id) {
        $select = $this->select()->where('giver_id = ?', $user_id)
        ->where('status = ?', 'given')
        ->order('given_date DESC');
        return $this->fetchAll($select);
    }
    
    public function getRecommendationsOfOccupation($type, $id, $user_id) {
        $select = $this->select()->where('receiver_id = ?', $user_id)
            ->where('receiver_position_type = ?', $type)
            ->where('receiver_position_id = ?', $id)
            ->where('status = ?', 'given')
            ->order('given_date DESC');
        return $this->fetchAll($select);
    }
    
    public function getShowRecommendationsOfOccupation($type, $id, $user_id) {
        $select = $this->select()->where('receiver_id = ?', $user_id)
            ->where('receiver_position_type = ?', $type)
            ->where('receiver_position_id = ?', $id)
            ->where('status = ?', 'given')
            ->where('`show` = ?', 1)
            ->order('given_date DESC');
        return $this->fetchAll($select);
    }
    
    public function getAskRecommendations($user_id) {
        $select = $this->select()->where('receiver_id = ?', $user_id)
            ->where('status = ?', 'ask');
        return $this->fetchAll($select);
    }

    public function getAskedRecommendation($giver_id, $type, $id) {
        $select = $this->select()->where('giver_id = ?', $giver_id)
            ->where('receiver_position_type = ?', $type)
            ->where('receiver_position_id = ?', $id)
            ->where('status = ?', 'ask');
        return $this->fetchRow($select);
    }

    public function getPendingRecommendaions($user_id) {
        $select = $this->select()->where('giver_id = ?', $user_id)
            ->where('status = ?', 'ask')
            ->order('creation_date DESC');
        return $this->fetchAll($select);
    }
    
    public function getExperienceIds($giver_id, $receiver_id) {
        $select = $this->select()->where('giver_id = ?', $giver_id)
            ->where('receiver_id = ?', $receiver_id)
            ->where('receiver_position_type = ?', 'experience')
            ->where('status = ?', 'given');
        $recommendations =  $this->fetchAll($select);
        $result = array();
        foreach ($recommendations as $recommendation) {
            $result[] = $recommendation->receiver_position_id;
        }
        return $result;
    }
    
    public function getEducationIds($giver_id, $receiver_id) {
        $select = $this->select()->where('giver_id = ?', $giver_id)
            ->where('receiver_id = ?', $receiver_id)
            ->where('receiver_position_type = ?', 'education')
            ->where('status = ?', 'given');
        $recommendations =  $this->fetchAll($select);
        $result = array();
        foreach ($recommendations as $recommendation) {
            $result[] = $recommendation->receiver_position_id;
        }
        return $result;
    }
    
    public function removeRecommendationsByItem($type, $id) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('receiver_position_type = ?', $type);
        $where[] = $this->getAdapter()->quoteInto('receiver_position_id = ?', $id);
        $this->delete($where);
    }
	
	public function removeRecommendationsOfReceiver($receiver_id) {
		$where = $this->getAdapter()->quoteInto('receiver_id = ?', $receiver_id);
        $this->delete($where);
	}
	
	public function removeRecommendationsOfGiver($giver_id) {
		$where = $this->getAdapter()->quoteInto('giver_id = ?', $giver_id);
        $this->delete($where);
	}
}
