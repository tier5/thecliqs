<?php
class Ynresume_Widget_SkillController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
	   	$this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
		if (is_null($resume))
		{
			return $this->setNoRender();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			return $this -> setNoRender();
		}
		$this->view->owner = $owner = $resume -> getOwner();
		$skillTbl = Engine_Api::_()->getDbtable('skills', 'ynresume');
		$totalSkills = $skillTbl->getSkillsByUser($resume, $owner);
		$arr = array();
		foreach ($totalSkills as $skill)
		{
			$item = $skill -> toArray();
			$item['endorses'] = $skill -> getEndorsedUsers($resume);
			if (count($item['endorses']))
			{
				foreach ($item['endorses'] as $endorse)
				{
					$item['endorsed_user_ids'][] = $endorse -> user_id;
				}
			}
			else 
			{
				$item['endorsed_user_ids'] = array();
			}
			$arr[] = $item;
		}
		$this -> view -> skills = $arr;
	}
}
