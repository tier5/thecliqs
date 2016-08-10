<?php
class Ynultimatevideo_Widget_ListTagsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $t_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
    $b_table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
    $tName = $t_table->info('name');
    $tmName = $tm_table->info('name');
    $bName = $b_table->info('name');

    $filter_select = $tm_table->select()->from($tmName,"$tmName.*")
        ->setIntegrityCheck(false)
        ->joinLeft($bName,"$bName.video_id = $tmName.resource_id",'');

    $select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
    $select -> joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
    $select -> order("$tName.text");
    $select -> group("$tName.text");
    $select -> where("t.resource_type = ?","ynultimatevideo_video");

    if(Engine_Api::_()->core()->hasSubject('user')){
      $user = Engine_Api::_()->core()->getSubject('user');
      $select -> where("t.tagger_id = ?", $user->getIdentity());
    }
    else if( Engine_Api::_()->core()->hasSubject('ynultimatevideo_video') ) {
      $video = Engine_Api::_()->core()->getSubject('ynultimatevideo_video');
      $user = $video->getOwner();
      $select -> where("t.tagger_id = ?", $user->getIdentity());
    }

    $result = $t_table->fetchAll($select);
    if (count($result) == 0) {
      return $this->setNoRender();
    }
    $this->view->tags = $result;
  }
}