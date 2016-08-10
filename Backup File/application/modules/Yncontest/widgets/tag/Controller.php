<?php
class Yncontest_Widget_TagController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$t_table = Engine_Api::_()->getDbtable('tags', 'core');
        $tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $p_table = Engine_Api::_()->getDbTable('contests', 'yncontest');
        $tName = $t_table->info('name');
        $tmName = $tm_table->info('name');
        $pName = $p_table->info('name');

        $filter_select = $tm_table->select()->from($tmName,"$tmName.*")
                         ->setIntegrityCheck(false)
                         ->joinLeft($pName,"$pName.contest_id = $tmName.resource_id",'');
                         //->where("$pName.publish_status <> 'draft'");
        
        $select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
        $select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
        $select  ->order("$tName.text");
        $select  ->group("$tName.text");
        $select  ->where("t.resource_type = ?","contest");
		
		
        $this->view->tags = $tags = $t_table->fetchAll($select);
		
        if(count($tags) <= 0)
        {
            $this->setNoRender();
        }
	}
}
