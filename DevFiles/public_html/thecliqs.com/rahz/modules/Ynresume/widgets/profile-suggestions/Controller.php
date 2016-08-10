<?php
class Ynresume_Widget_ProfileSuggestionsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		
	 	 // Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject('ynresume_resume');
        if (!$subject -> isViewable()) {
            return $this -> setNoRender();
        }
		
		$num_of_resumes = $this -> _getParam('num_of_resumes', 10);
        if (!$num_of_resumes) {
            $num_of_resumes = 6;
        }
		
		$subject_id = $subject->getIdentity();
		$table = Engine_Api::_()->getItemTable('ynresume_resume');
		$select = $table->select()
			->where("headline LIKE ? AND resume_id <> $subject_id", '%'.$subject->title.'%')
			->orWhere("headline LIKE ? AND resume_id <> $subject_id", '%'.$subject->headline.'%')
        	->orWhere("industry_id = ? AND resume_id <> $subject_id", $subject->industry_id)
			->order("rand()")
			->limit($num_of_resumes);
		$this->view->resumes = $resumes = $table->fetchAll($select);
		if (!count($resumes)) {
			return $this->setNoRender();
		}
	}
}
	