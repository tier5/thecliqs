<?php

class Headvancedalbum_Widget_FriendsAlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return $this->setNoRender();
    }

    $table = Engine_Api::_()->getDbTable('albums', 'album');
    $tableName = $table->info('name');
    $data = $viewer->membership()->getMembershipsOfIds(); // friends ids
    if (empty($data)){
      return $this->setNoRender(); // no friends :(
    }


    $request = Zend_Controller_Front::getInstance()->getRequest();
    $hide_ids = $request->getParam('hide_ids');

    // Check user data
    $prepare_hide_ids = array(0);
    if ($hide_ids){
      foreach ($hide_ids as $id){
        $prepare_hide_ids[] = (int) $id;
        if (count($prepare_hide_ids) > 500){ // maybe ;)
          break;
        }
      }
    }


    $allowTable = Engine_Api::_()->getDbTable('allow', 'authorization');
    $allowTableName = $allowTable->info('name');


    $per_page = $this->_getParam('itemCountPerPage', 3);
    $the_end = false;

    $select = $table->select()
      ->from(array('p' => $tableName), new Zend_Db_Expr('p.*'))
      // Check privacy
      ->join(array('a' => $allowTableName), 'a.resource_type = "album" AND a.resource_id = p.album_id AND a.action = "view" AND a.role IN ("everyone", "registered", "owner_member")', array())
      ->where('p.owner_type = "user"')
      ->where('p.owner_id IN (?)', $data) // is my friend
      ->where('p.album_id NOT IN (?)', $prepare_hide_ids)
      ->group('p.album_id')
      ->order('RAND()') // TODO check perfomance
      ->limit($per_page+1); // take one more to check the end



    $data = $table->fetchAll($select);
    $count = count($data);
    $items = array();


    if (!$count) {
      return $this->setNoRender(); // no items
    }

    for ($i = 0; $i < $count; $i++) {
      if ($i >= $count-1) { // if it's the last
        if ($per_page == $i) { // item list is fully
          break; // cut the last
        } else {
          // it's not fully then here no more
          $the_end = true;
        }

      }
      $items[] = $data[$i];
    }

    $this->view->paginator = $items;
    $this->view->the_end = $the_end;







  }
}