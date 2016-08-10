<?php
class Ynresume_Widget_RecommendationAskController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $this->setNoRender();
            return;
        }
        
        $params = $this->_getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        $this->view->occupations = $occupations = Engine_Api::_()->ynresume()->getOccupations();
        
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max_giver = $permissionsTable->getAllowed('ynresume_resume', $viewer->level_id, 'max_friend');
        if ($max_giver == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynresume_resume')
            ->where('name = ?', 'max_friend'));
            if ($row) {
                $max_giver = $row->value;
            }
        }
        $this->view->max_giver = $max_giver;
        if (isset($params['send']) && $params['send']) {
            $giverIds = explode(',', $params['giver_ids']);
            $table = Engine_Api::_()->getDbTable('recommendations', 'ynresume');
            $db = $table->getAdapter();
            $db->beginTransaction();
            $receiver_occupation = explode('-', $params['occupation']);
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $values  = array(
                'receiver_id' => $viewer->getIdentity(),
                'receiver_position_type' => $receiver_occupation[0],
                'receiver_position_id' => $receiver_occupation[1],
                'ask_subject' => strip_tags($params['ask_subject']),
                'ask_message' => $params['ask_message'],
                'status' => 'ask',
                'ask_date' => date('Y-m-d H:i:s')
            );
            try {
                foreach ($giverIds as $id) {
                    $values['giver_id'] = $id;
                    $values['relationship'] = (isset($params['relationship-'.$id]) && $params['relationship-'.$id]) ? $params['relationship-'.$id] : null;
                    $giver_occupation = (isset($params['occupation-'.$id]) && $params['occupation-'.$id]) ? explode('-',$params['occupation-'.$id]) : null;;
                    $values['giver_position_type'] = ($giver_occupation) ? $giver_occupation[0] : null;
                    $values['giver_position_id'] = ($giver_occupation) ? $giver_occupation[1] : null;
                    if ($values['relationship']) {
                        $recommendation = $table->createRow();
                        $recommendation->setFromArray($values);
                        $recommendation->save();
                        
                        //send notifications
                        $giver = Engine_Api::_()->user()->getuser($id);
                        $email_p = array(
                            'ask_subject' => $recommendation->ask_subject, 
                            'ask_message' => $recommendation->ask_message,
                            'position' => Engine_Api::_()->ynresume()->getPosition($recommendation->receiver_position_type, $recommendation->receiver_position_id)
                        );
                        if ($giver)
                            $notifyApi -> addNotification($giver, $viewer, $recommendation, 'ynresume_asked_recommendation', $email_p);
                    }
                }
                $db->commit();
                $params = array();
                $this->view->success = true;
            }
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        $this->view->params = $params;
	}
}