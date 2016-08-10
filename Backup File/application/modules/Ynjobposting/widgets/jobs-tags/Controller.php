<?php
class Ynjobposting_Widget_JobsTagsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
        $tag_map_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $job_table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $tag_name = $tag_table->info('name');
        $tag_map_name = $tag_map_table->info('name');
        $job_name = $job_table->info('name');
    
        $filter_select = $tag_map_table->select()->from($tag_map_name,"$tag_map_name.*")
            ->setIntegrityCheck(false)
            ->joinLeft($job_name,"$job_name.job_id = $tag_map_name.resource_id",'')
            ->where("$job_name.status = ?","published");
    
        $select = $tag_table->select()->from($tag_name,array("$tag_name.*","Count($tag_name.tag_id) as count"));
        $select  ->joinLeft($filter_select, "t.tag_id = $tag_name.tag_id",'');
        $select  ->order("$tag_name.text");
        $select  ->group("$tag_name.text");
        $select  ->where("t.resource_type = ?","ynjobposting_job");
    
        if(Engine_Api::_()->core()->hasSubject('user')){
          $user = Engine_Api::_()->core()->getSubject('user');
          $select -> where("t.tagger_id = ?", $user->getIdentity());
        }
        else if( Engine_Api::_()->core()->hasSubject('ynjobposting_job') ) {
          $job = Engine_Api::_()->core()->getSubject('ynjobposting_job');
          $user = $job->getOwner();
          $select -> where("t.tagger_id = ?", $user->getIdentity());
        }
        if (isset($user)) {
            $this->view->user = $user->getIdentity();
        }
    	$tags = $tag_table->fetchAll($select);
		if(count($tags) < 1)
		{
			return $this->setNoRender();
		}
        $this->view->tags = $tags;
    }
}
?>
