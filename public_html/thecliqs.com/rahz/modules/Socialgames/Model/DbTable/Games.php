<?php

class Socialgames_Model_DbTable_Games extends Engine_Db_Table{

	protected $_rowClass = 'Socialgames_Model_Game';
	
	public function getGamesSelect($params = array())
    {
		$table = Engine_Api::_()->getDbtable('games', 'socialgames');
		$rName = $table->info('name');
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		
		$select = $table->select()->from($rName);
		if( !empty($params['is_favourite']))
		{
			$ftable = Engine_Api::_()->getDbtable('favourite', 'socialgames');
			$fName = $ftable->info('name');
			$select->joinLeft($fName, "$rName.game_id = $fName.game_id", NUll)->where("$fName.user_id = ?",$user_id);
		}
		if (empty($params['orderby']) )
		{
		  $select->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $rName.'.game_id DESC' );
		}
		if ($params['orderby']=="most_recent" )
		{
		  $select->order( $rName.'.game_id DESC' );
		}
		if( $params['orderby']=="view_count")
		{
			$select->order($rName.'.total_views DESC' );
		}
		if( $params['orderby']=="most_played")
		{
			$select->order($rName.'.total_members DESC' );
		}
		if( $params['orderby']=="RAND()")
		{
			$select->order('RAND()');
		}
		
		if( !empty($params['category']))
		{
		  $select->where($rName.'.category = ?', $params['category']);
		}
		
		if( !empty($params['is_active']) )
		{
		  $select->where($rName.'.is_active = ?', $params['is_active']);
		}
		
		if( !empty($params['is_featured']) )
		{
		  $select->where($rName.'.is_featured = ?', $params['is_featured']);
		}

		//else $select->group("$rName.blog_id");

		// Could we use the search indexer for this?
		if( !empty($params['search']) )
		{
		  $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$params['search'].'%');
		}
		
		return $select;
    } 
  
	public function getGamesPaginator($params = array())
    {
		$paginator = Zend_Paginator::factory($this->getGamesSelect($params));
		if( !empty($params['page']) )
		{
		  $paginator->setCurrentPageNumber($params['page']);
		}
		if( !empty($params['limit']) )
		{
		  $paginator->setItemCountPerPage($params['limit']);
		}
		return $paginator;
   }
   
   public function getCategoriesAssoc()
   {
		$table = Engine_Api::_()->getDbtable('games', 'socialgames');
		$select = $table->select("")
		 ->group("category")
		 ->query();
		
		$data = array();
		foreach( $select->fetchAll() as $category ) {
		  $data[$category['category']] = $category['category'];
		}
		return $data;
   }
}