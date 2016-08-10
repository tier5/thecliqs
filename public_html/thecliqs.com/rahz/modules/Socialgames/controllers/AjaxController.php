<?php

class Socialgames_AjaxController extends Core_Controller_Action_Standard
{
    public function addfavouriteAction()
    {
		$viewer = Engine_Api::_()->user()->getViewer();
        $game_id = $this->_getParam('game_id');
		$user_id = $viewer->getIdentity();
		
        $table = Engine_Api::_()->getDbtable('favourite', 'socialgames');
		$row = $table->fetchRow(array('game_id =?' => $game_id, 'user_id =?' => $user_id));
		if ($row)
		{
			$db = $table->getAdapter();
			$db->beginTransaction();
			try
			{
				$row->delete();
				$db->commit();
				$status = 1;
			}
			catch (Exception $error){
				$db->rollBack();
				throw $error;
			}
		}
		else
		{
			$db = $table->getAdapter();
			$db->beginTransaction();
			try
			{
				$newrow = $table->createRow();
				$newrow->setFromArray(array("game_id" => $game_id,"user_id" => $user_id));
				$newrow->save();
				$db->commit();
				$status = 0;
			}
			catch (Exception $error){
				$db->rollBack();
				throw $error;
			}
		}
        $this->view->status = $status;

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
	
	public function playAction()
    {
		$viewer = Engine_Api::_()->user()->getViewer();
        $game_id = $this->_getParam('game_id');
		$user_id = $viewer->getIdentity();
		
        $table = Engine_Api::_()->getDbtable('play', 'socialgames');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try
		{
			$newrow = $table->createRow();
			$newrow->setFromArray(array("game_id" => $game_id,"user_id" => $user_id));
			$newrow->save();
			$db->commit();
			$status = 1;
		}
		catch (Exception $error){
			$db->rollBack();
			throw $error;
		}
		
		//ADD PHOTO ID
		$gameTable = Engine_Api::_()->getDbtable('games', 'socialgames');
		$game = Engine_Api::_()->getItem('socialgames_game', $game_id);
		if (!$game->photo_id)
		{
			$photo_params = array(
				'parent_id' => 1,
				'parent_type' => 'socialgames_game',
			);
			$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

			$mainName = $path . '/m_' . time().".jpg";
			$mainName1 = $path . '/m_' . time()."1.jpg";
			$image = Engine_Image::factory();
			
			$url = file_get_contents(str_replace("https://","http://",$game->image));
			file_put_contents($mainName,$url);
			
			$image->open($mainName)->write($mainName1)->destroy();

			$photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
			$photo_id = $photoFile->getIdentity();
			$gameTable->update(array(
			'photo_id' => $photo_id
		  ), array(
			'game_id = ?' => $game_id,
		  ));
		}
		
		//INCREASE TOTAL MEMBERS
		$gameTable->update(array(
			'total_members' => new Zend_Db_Expr('total_members + 1')
		  ), array(
			'game_id = ?' => $game_id,
		  ));
		
        $this->view->status = $status;
		
		
			
		$game = Engine_Api::_()->getItem('socialgames_game', $game_id);
		$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $game, 'socialgames_new');
		
        if( $action ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $game);
        }
		
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}