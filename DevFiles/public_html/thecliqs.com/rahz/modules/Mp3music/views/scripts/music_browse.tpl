<?php
$this->headScript()
	   ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
	   ?>
       <h3><?php if($this->browse->params['search'] == 'browse_topsongs'): echo $this->translate("Top songs"); endif;
            if($this->browse->params['search'] == 'browse_topdownloads'): echo $this->translate("Top downloads"); endif;
            if($this->browse->params['search'] != 'browse_topsongs' && $this->browse->params['search'] != 'browse_topdownloads'): echo $this->translate("Song list"); endif;?>    
        </h3>                        
         <ul style="" class = "mp3music_table">
                <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <th height="25px" width="68%" style="padding:2px 2px 2px 7px;"><?php echo $this->translate("Songs/Singers");?></th>
                    <th style="padding:2px;text-align:center"><?php echo $this->translate('') ?></th> 
                    <th style="padding:2px;text-align:center"><?php echo $this->translate('Author') ?></th>
                </tr>
			<?php  $songs  = $this->browse->songPaginator;?>
			  <?php foreach ($songs as $song): 
              $info = $song->getInfo($song); 
               $album = Engine_Api::_()->getItem('mp3music_album', $song->album_id);
                ?>
			   <tr id="song_item_<?php echo $song->getIdentity() ?>">
			    <td class="mp3_title_link" width="65%" style="padding:7px;">
                       <a title="<?php echo $song->title ?>" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$song->album_id,'song_id'=>$song->song_id), 'mp3music_album_song');?>',800,565)">
                       <?php echo strlen($song->title)>40?substr($song->title,0,37).'...':$song->title;?> 
                       </a>  
                       <div  class="mp3_album_info_browse" style="padding-top:5px;">
                        <?php echo $this->translate('Album:'); ?>
                       <a href="javascript:;"  onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',800,565)"><?php echo strlen($album->title)>40?substr($album->title,0,37).'...':$album->title;?></a>
                       </div>
                        <div  class="mp3_album_info_browse" style="padding-top:5px;">
                          <?php echo $this->translate('Singer:');  if($song->singer_id == 0 && $song->other_singer == ""):
                                    echo $this->translate(" Not Update"); 
                                endif;   ?> 
                            <?php if($song->singer_id != 0):  
                             $singer =  Engine_Api::_()->getItem('mp3music_singer', $song->singer_id);
                              if($singer):     
                                    echo $this->htmlLink($this->url(array('search'=>'singer','id'=>$singer->singer_id,'title'=>null,'type'=>null), 'mp3music_search'),
                                    strlen($singer->title)>20?substr($singer->title,0,20).'...':$singer->title,
                                    array('class'=>''));
                                 else:
                                    echo $this->translate(" Not Update");
                                     endif; 
                             endif;
                            if($song->other_singer != "" && $song->singer_id == 0): 
                            echo $this->htmlLink($this->url(array('search'=>'singer','title'=>$song->other_singer,'id'=>null), 'mp3music_search'),
                                    strlen($song->other_singer)>20?substr($song->other_singer,0,20).'...':$song->other_singer,
                                    array('class'=>''));
                            endif; ?></a>
                        </div>
                    </td>
                    <td style="padding:7px;" class="mp3_album_info_browse">
                    <a class="mp3music_playmusic" href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$song->album_id,'song_id'=>$song->song_id), 'mp3music_album_song');?>',800,565)"><img src="./application/modules/Mp3music/externals/images/music/icon_play.png" /></a>
                    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != "0"):
                          echo $this->htmlLink(
                           $this->url(array('playlist_id'=>'0','song_id'=>$song->song_id), 'mp3music_playlist_append'),
                            '<img src="./application/modules/Mp3music/externals/images/music/icon_add.png" />',
                            array('class'=>'smoothbox music_player_tracks_add') );  endif;?>         
                    <?php
                    $user = Engine_Api::_()->user()->getViewer();
                    $allowed_download = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_album', $user, 'is_download');
                    $allowed_view = (int) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_album', $user, 'view');
                    if($album->is_download == 1 && $allowed_download == true && $allowed_view != 0 ):
                        echo $this->htmlLink('./application/modules/Mp3music/externals/scripts/download.php?idsong='.$song->getIdentity().'&f='.$song->getFilePath().'&fc=\''.$song->title.'.mp3\'','<img src="./application/modules/Mp3music/externals/images/music/icon_download.png" />',array(
                          'class'=>'music_player_tracks_url',
                          'type'=>'audio',
                          'rel'=>$song->song_id) );
                    else: ?>
                         <img src="./application/modules/Mp3music/externals/images/music/icon_download_disable.png"/>
                   <?php endif; ?> 
                   
                <br> 
                     <?php echo $song->play_count.$this->translate(" listens")?>
                           <br>
                           <?php 
                           echo $info['Length mm:ss']." | ".$info['Bitrate']."kb/s";
                           ?>  
                    </div>                    
                    </td>
					<td  align="center" style="padding:5px 5px 5px 15px;">
                        <div class="mp3_image">
                        <a title="<?php  echo $album->getOwner()->getTitle();?>" href="<?php echo $album->getOwner()->getHref()?>">
                        <?php echo $this->itemPhoto($album->getOwner(), 'thumb.icon'); ?>
                        </a>
                        <br />
                    </div>
                    </td>
				</tr>
					 <?php endforeach; ?>
                </table> 
                    <span style="padding:10px; float:right ;"> <?php echo $this->paginationControl($this->browse->songPaginator,null, null, array(
				      'pageAsQuery' => false,
				      'query' => array("search"=>$this->browse->params['search'], "title"=>$this->browse->params['title']),
				    ))?>  </span> 
                <br/>  
                 <?php if (0 == count($songs) ): ?>
                <div class="tip" style="padding-left: 20px;">
                <span>
                <?php if($this->browse->params['title']): 
                echo $this->translate('Nobody has uploaded a song with that criteria.');
                else:
                    echo $this->translate('Nobody has uploaded a song yet.');
                endif;?> 
                </span>
                </div>
                <?php endif;  ?>   
  </ul>                