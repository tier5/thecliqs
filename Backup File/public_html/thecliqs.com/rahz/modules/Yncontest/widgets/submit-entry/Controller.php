<?php
class Yncontest_Widget_SubmitEntryController extends Engine_Content_Widget_Abstract
{
	private $_configureVideo = '<object width="560" height="340" type="application/x-shockwave-flash" data="/project/externals/flowplayer/flowplayer-3.1.5.swf"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="high" name="quality"><param value="transparent" name="wmode"><param value="config={&quot;clip&quot;:{&quot;url&quot;:&quot;@#$%videoconfigure%$#@?c=dd8a&quot;,&quot;autoPlay&quot;:false,&quot;duration&quot;:&quot;0&quot;,&quot;autoBuffering&quot;:true},&quot;plugins&quot;:{&quot;controls&quot;:{&quot;background&quot;:&quot;#000000&quot;,&quot;bufferColor&quot;:&quot;#333333&quot;,&quot;progressColor&quot;:&quot;#444444&quot;,&quot;buttonColor&quot;:&quot;#444444&quot;,&quot;buttonOverColor&quot;:&quot;#666666&quot;}},&quot;canvas&quot;:{&quot;backgroundColor&quot;:&quot;#000000&quot;}}" name="flashvars"></object>	';
	
	private $_configVideoNew = '			
			<div class="ynContest_view ynContest_view_container">
            	<div class="ynContest_embed">
    			</div>
			</div>		
			';

	public function indexAction()
	{			
		$this->getElement()->removeDecorator('Title');		
		$viewer = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance() -> getRequest();		
		$contestId = $request->getParam('contestId');
		
		if(empty($contestId))
		{			
			return $this->setNoRender();
		}
		
		$submit = $request->getParam('submit', 0);
		
		if(empty($submit)){			
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$contest = Engine_Api::_()->getItem('contest', $contestId);	
		
		$this->view->height = (int)$this -> _getParam('height',102);
		$this->view->width = (int)$this -> _getParam('width',134);
		$this->view -> items_per_page = $items_per_page = (int)$this -> _getParam('max'.$contest->contest_type,12);
		$this->view->item_name = $request->getParam('item_name', '');
		if($this->view->item_name == "View All")
			$this->view->item_name = '';
		switch ($contest->contest_type) {
			case 'ynblog':
				$plugin = Engine_Api::_()->yncontest()->getPluginsBlog();
				$paginator = Engine_Api::_()->yncontest()->getEntriesBlog(array('item_id' => $request->getParam('item_id',0), 'owner_id' => $viewer->getIdentity()));
				break;
			case 'advalbum':
				$plugin = Engine_Api::_()->yncontest()->getPluginsAlbum();
				
				$atable = Engine_Api::_() -> getDbtable('albums', $plugin);
				$aName = $atable -> info('name');
				$select = $atable -> select() -> where("$aName.owner_id =?", $viewer->getIdentity());
				$this->view->albums = $atable->fetchAll($select);
				$album_id = $request->getParam('album_id', 0);				
				if(empty($album_id))
					foreach($this->view->albums  as $album)
					{					
						$album_id = $album->getIdentity();					
					}		
					
				$this->view->album_id = $album_id;
				$paginator = Engine_Api::_()->yncontest()->getEntriesAlbum(array('owner_id' => $viewer->getIdentity(), 'album_id' => $album_id));				
				break;
			case 'ynvideo':
				$plugin = Engine_Api::_()->yncontest()->getPluginsVideo();
				$paginator = Engine_Api::_()->yncontest()->getEntriesVideo(array('item_id' => $request->getParam('item_id',0), 'owner_id' => $viewer->getIdentity()));
				break;
			case 'mp3music':
				$plugin = Engine_Api::_()->yncontest()->getPluginsMusic();	
				$paginator = Engine_Api::_()->yncontest()->getEntriesMusic(array('item_id' => $request->getParam('item_id',0), 'user_id' => $viewer->getIdentity()));					
				break;
			case 'ynmusic':
				$plugin = Engine_Api::_()->yncontest()->getPluginsSocialMusic();
				$paginator = Engine_Api::_()->yncontest()->getEntriesSocialMusic(array('item_id' => $request->getParam('item_id',0), 'user_id' => $viewer->getIdentity()));
				break;
			case 'ynultimatevideo':
				$plugin = Engine_Api::_()->yncontest()->getPluginsUltimateVideo();
				$paginator = Engine_Api::_()->yncontest()->getEntriesUltimatevideo(array('item_id' => $request->getParam('item_id',0), 'user_id' => $viewer->getIdentity()));
				break;
		}	

		$paginator->setCurrentPageNumber( $request->getParam('page', 1));
		$paginator->setItemCountPerPage($items_per_page);
			
		// check plugin exits
		if(empty($plugin)){
			return $this->setNoRender();
		} 
		$this->view->paginator = $paginator;
		
		
		
		if(!$contest -> membership() -> isMember($viewer, true))  return $this->setNoRender();

		$this->view->form = $form = new Yncontest_Form_Submit_Item();

		$entries = Engine_Api::_()->getItemTable('yncontest_entries')->find($request->getParam('id', null))->current();
		if($entries){

			// If not post or form not valid, return
			$array = $entries->toArray();			

			$form->populate($array);
		}
			
		$this->view->contest = $contest;
		$this->view->viewer = $viewer;		
		
		$page = $request->getParam('page', 0);		
		if(empty($page))
		{	
			if($request->isPost() && $form->isValid($request->getPost())){
				$post = $request->getPost();
				
				if(isset($_SESSION[$contest->contest_type]) && $_SESSION[$contest->contest_type]!=null){
					$table = new Yncontest_Model_DbTable_Entries;
					$db = $table -> getAdapter();
					$db -> beginTransaction();
					try {
						$now = date('Y-m-d H:i:s');
	
						$values = array_merge($form -> getValues(), array(
								'user_id' => $viewer -> getIdentity(),
								'contest_id' => $contest->contest_id,
								'entry_type'=> $contest->contest_type,
								'start_date' => $now,
						));
						$values['item_id'] =$_SESSION[$contest->contest_type];
				
						$setting = Engine_Api::_()->getDbtable('settings', 'yncontest')->getSettingByContest($contest->contest_id);
	
						//update numbers_entries;
						$setting->numbers_entries++;
						$setting->save();
						if($setting->entries_approve  == 1){
							$values['approve_status'] = 'approved';
							$values['entry_status'] = 'published';
							$values['approved_date'] = $now;
						}
						else{
							$values['approve_status'] = 'pending';
						}
						
						$entries = $table -> createRow();
	
						$entries -> setFromArray($values);
						
						$entries -> save();
						
						switch ($entries->entry_type) {
							case 'ynblog':
								$blogPlugin = Engine_Api::_()->yncontest()->getPluginsBlog();
								$obj = Engine_Api::_() -> getDbtable('blogs', $blogPlugin) -> find($entries->item_id) -> current();
								$entries->content = $obj->body;
							
								break;
	
							case 'advalbum':
								//dupplicatte photo
								$albumPlugin = Engine_Api::_()->yncontest()->getPluginsAlbum();
								
								$table = Engine_Api::_() -> getItemTable($albumPlugin.'_photo' );
								$select = $table -> select() -> where('photo_id =?', $entries->item_id);
								$result = $table->fetchRow($select);
								$file = Engine_Api::_()->getDbtable('files', 'storage')->find($result->file_id)->current();
								if($file){
									$photo = $contest->createPhoto($contest->album_id, $file);
	
									$view = Zend_Registry::get('Zend_View');								
									$entries->content = $photo->file_id;
									$entries->photo_id = $photo->file_id;
									
								}
								break;
	
							case 'ynvideo':
								
								$obj = Engine_Api::_() -> getItemTable('video') -> find($entries->item_id) -> current();
	
								//create photo thumnail
								$file = Engine_Api::_()->getDbtable('files', 'storage')->find($obj->photo_id)->current();
								if($file){
									$photo = $contest->createPhoto($contest->album_id, $file);
									$entries->photo_id = $photo;
									
								}
	
								$rawContent = $obj->getRichContent();
								if ($obj->type != 3) {
									preg_match_all('/<object[^>]*?>[\s\S]*?<\/object>/', $rawContent, $matches);
									if (!(is_array($matches) && !empty($matches))) {
										preg_match_all('/<iframe[^>]*?>[\s\S]*?<\/iframe>/', $rawContent, $matches);
									}
									$data = $matches;
									while (is_array($data)) {
										$data = current($data);
									}
									if (!empty($data)) {
										$entries->content = 
										'<div class="video_view video_view_container">
										<div class="video_embed">'.$data.
										'</div>
										</div>';
									} else {
										$entries->content = $rawContent;
									}
								} else {
									//duplicate video upload
									$file = Engine_Api::_()->getDbtable('files', 'storage')->find($obj->file_id)->current();
									if($file){
										$file_name = $contest->createVideo($entries->entry_name, $file);
										$baseUrl = $this->view->layout()->staticBaseUrl;
                                        $configureVideo = '<object width="560" height="340" type="application/x-shockwave-flash" data="'.$baseUrl.'externals/flowplayer/flowplayer-3.1.5.swf"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="high" name="quality"><param value="transparent" name="wmode"><param value="config={&quot;clip&quot;:{&quot;url&quot;:&quot;@#$%videoconfigure%$#@?c=dd8a&quot;,&quot;autoPlay&quot;:false,&quot;duration&quot;:&quot;0&quot;,&quot;autoBuffering&quot;:true},&quot;plugins&quot;:{&quot;controls&quot;:{&quot;background&quot;:&quot;#000000&quot;,&quot;bufferColor&quot;:&quot;#333333&quot;,&quot;progressColor&quot;:&quot;#444444&quot;,&quot;buttonColor&quot;:&quot;#444444&quot;,&quot;buttonOverColor&quot;:&quot;#666666&quot;}},&quot;canvas&quot;:{&quot;backgroundColor&quot;:&quot;#000000&quot;}}" name="flashvars"></object>';
										$entries->content = str_replace("@#$%videoconfigure%$#@", $file_name, $configureVideo) ;
									}
								}	
								
								break;
							case 'mp3music':
								
								$paginator = Engine_Api::_()->yncontest()->getEntriesMusic(array('item_id' => $_SESSION['music_type'].$_SESSION[$contest->contest_type]));
								
								foreach ($paginator as $key => $item) {
									if(is_array($item))
									{
										$song = Engine_Api::_()->getItemTable($item['resource_type'])->find($item['song_id'])->current();
										
							    		if($item['resource_type'] == 'music_playlist_song')
							    		{	            			
							    			$obj = Engine_Api::_()->getItemTable('music_playlist')->find($song->playlist_id)->current();	            			
							    		}
							    		else{
							    			$obj = Engine_Api::_()->getItemTable('mp3music_album')->find($song->album_id)->current();
							    		}
										
										//get url song
										$file = Engine_Api::_()->getDbtable('files', 'storage')->find($song->file_id)->current();
										$entries->content = $file->map();										
									} 
								}																
								
								//create photo thumnail
								$file = Engine_Api::_()->getDbtable('files', 'storage')->find($obj->photo_id)->current();
									
								if($file){									
									$photo = $contest->createPhoto($contest->album_id, $file);									
									$entries->photo_id = $photo;									
								}	
								break;

							case 'ynmusic':
								$song = Engine_Api::_() -> getItemTable('ynmusic_song') -> find($entries->item_id) -> current();

								//create photo thumnail
								$file = Engine_Api::_()->getDbtable('files', 'storage')->find($song->photo_id)->current();
								if($file){
									$entries->photo_id = $song->photo_id;
								}

								//get url song
								$entries->content = $song->getFilePath();
								break;

							case 'ynultimatevideo':
								$obj = Engine_Api::_() -> getItemTable('ynultimatevideo_video') -> find($entries->item_id) -> current();

								//create photo thumnail
								$file = Engine_Api::_()->getDbtable('files', 'storage')->find($obj->photo_id)->current();
								if($file){
									$photo = $contest->createPhoto($contest->album_id, $file);
									$entries->photo_id = $photo;
								}

								$rawContent = $obj->getRichContent(true);
								if ($obj->type != 3) {
									$entries->content = $rawContent;
								} else {
									//duplicate video upload
									$file = Engine_Api::_()->getDbtable('files', 'storage')->find($obj->file_id)->current();
									if($file){
										$file_name = $contest->createVideo($entries->entry_name, $file);
										$baseUrl = $this->view->layout()->staticBaseUrl;
										$configureVideo = '<object width="560" height="340" type="application/x-shockwave-flash" data="'.$baseUrl.'externals/flowplayer/flowplayer-3.1.5.swf"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="high" name="quality"><param value="transparent" name="wmode"><param value="config={&quot;clip&quot;:{&quot;url&quot;:&quot;@#$%videoconfigure%$#@?c=dd8a&quot;,&quot;autoPlay&quot;:false,&quot;duration&quot;:&quot;0&quot;,&quot;autoBuffering&quot;:true},&quot;plugins&quot;:{&quot;controls&quot;:{&quot;background&quot;:&quot;#000000&quot;,&quot;bufferColor&quot;:&quot;#333333&quot;,&quot;progressColor&quot;:&quot;#444444&quot;,&quot;buttonColor&quot;:&quot;#444444&quot;,&quot;buttonOverColor&quot;:&quot;#666666&quot;}},&quot;canvas&quot;:{&quot;backgroundColor&quot;:&quot;#000000&quot;}}" name="flashvars"></object>';
										$entries->content = str_replace("@#$%videoconfigure%$#@", $file_name, $configureVideo) ;
									}
								}	
								break;
						}
						
						$entries->save();
						if($setting->entries_approve  == 1){
	
							$entries-> sendNotMailOwner($viewer, $viewer, 'submit_entry', null);
	
							if($viewer->user_id != $contest -> user_id){
								$user = Engine_Api::_() -> user() -> getUser($contest -> user_id);
								$entries-> sendNotMailOwner($user, $viewer, 'submit_entry_f', null);
							}							
						}
					}
					catch( Exception $e ) {
						$db -> rollBack();
						throw $e;
					}
						
					$roles = array('owner','yncontest_list', 'parent_member', 'member', 'registered', 'everyone');
					$auth = Engine_Api::_() -> authorization() -> context;
					$commentEntriesMax = array_search('parent_member', $roles);
						
					$organizerList = $contest->getOrganizerList();
					foreach( $roles as $i => $role ) {
	
						if( $role === 'yncontest_list' ) {
							$role = $organizerList;
						}
						$auth->setAllowed($entries, $role, 'comment', ($i <= $commentEntriesMax));
	
					}
					unset($_SESSION[$contest->contest_type]);					
					$db -> commit();					
					Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoRoute(array('action' => 'view', 'contestId'=> $contest->contest_id), 'yncontest_mycontest', true);					
				}
				else{
					$form->addError('Please choose one item.');
				}
			
			}

		}
	}
}