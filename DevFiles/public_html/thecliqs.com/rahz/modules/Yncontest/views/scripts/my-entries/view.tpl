
<div class="wrapper_contest">
	<!-- View entry details -->
	<div class="yconstest_detail_voted">
		<div class="ycs_item_list entry_detail_left">
			<div class="large_item_info">				
				<h2><?php echo $this->htmlLink($this->item->getHref(), $this->item->getTitle()) ?></h2>
				<div class="extra_info">
					<p>
						<?php echo $this->translate("Author:") ?> 
						<span class="user_profile_link_span"><?php echo $this->htmlLink($this->item->getOwner()->getHref(), $this->item->getOwner()->getTitle()) ?>	</span>
					</p>
				</div>				
				<div class="large_item_action">
					<div class="ycvotes">
						<p><?php echo $this->item->vote_count ?></p>
					</div>
					<div class="ycviews">
						<p><?php echo $this->item->view_count ?></p>
					</div>
				</div>				
			</div>			
		</div>
		<div class="entry_detail_right">
			<div class="ynContest_entries_page_navigation">
				<?php if(isset($this->nextEntry)): ?>
					<?php echo $this->htmlLink($this->nextEntry->getHref(), $this->translate('Next'), array('id' => 'photo_next'));?>
				<?php else:?>
					<a href="javascript:void(0);" class="photo_next_end">Next</a>
				<?php endif;?>	
				
				
				<?php if($this->previousEntry):?>
					<?php echo $this->htmlLink($this->previousEntry->getHref(), $this->translate('Prev'), array('id' => 'photo_prev'));?> 
				<?php else:?>
					 <a href="javascript:void(0);" class="photo_prev_end">Preview</a>
				<?php endif;?>
				
				<div class="clear"></div>
			</div>
			<div class="ynContest_entries_vote">								
				<?php 
				if($this->item->entry_status == 'published' && $this->item->approve_status == 'approved'){ 
					if(Engine_Api::_() -> authorization() -> isAllowed('contest', $this->viewer(),'voteentries') && !$this->item->IsOwner($this->viewer()) && $this->item->checkVote() && ($this->contest->start_date_vote_entries <= date('Y-m-d H:i:s') && date('Y-m-d H:i:s') <= $this->contest->end_date_vote_entries ) ){
						 echo $this->htmlLink(array(
									'route' => 'yncontest_myentries',
									'action' => 'vote',
									'id' => $this->item->entry_id,
									'format' => 'smoothbox'),
									 $this->translate('Vote'),
									array('class' => 'buttonlink smoothbox'));
						}
				}  ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div class="ynContest_detailContentLeft">
		<?php if($this->item->entry_type == 'advalbum'):?>
			<?php echo $this->itemPhoto($this->item, 'thumb.main') ?>
		<?php elseif($this->item->entry_type == 'mp3music' || $this->item->entry_type == 'ynmusic'):?>
			<?php 
			 $this -> headScript() ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/m2bmusic_class.js')				  
				  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/jquery.js')
				  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/noconflict.js')
				  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/jquery-ui.js')
				  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/slimScroll.js')
				  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/mediaelement-and-player.min.js')
				  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/mp3music.js');
			?>
			<div id = "mp3music_reverse">		
				<ul id = "ynmp3music-wrapper">
					<div id = "ynmp3music-inner">   
							
				     <!-- HTML5 -->
							<div class="mp3music_container younet_html5_player init">
								<div class="yn-music">					
									<MARQUEE SCROLLDELAY="300">
											<span id="song-title-head"><?php echo $this->item->getTitle();?></span>
									</MARQUEE>
									<audio class="yn-audio-skin" class="mejs" width="100%" src="<?php echo $this->item->content;?>" type="audio/mp3" controls autoplay preload = "none"></audio>
								</div>
								<!-- Playlist -->
								<ul class="song-list mejs-list scroll-pane" id = "test_safari" style="display: none ">
									
										<li class="">
											<span class="song_id" ><?php echo $this->item->getIdentity();?></span>
											<span class="song_vote" ><?php echo $this->item->getIdentity();?></span>
											<span class = "isvote" ><?php echo $this->item->getIdentity();?></span>
											<span class="link"><?php echo $this->item->content;?></span>
											
											<a href="javascript:void(0)" onclick="_addPlaylist(<?php echo $this->item->getIdentity();?>)" class="yn-add-playlist">
												<?php echo $this->translate('Add to playlist');?>
											</a>
										</li>					
								</ul>
								<!-- End Playlist -->
							</div>
							<!-- END -->
					</div>
				</ul>          
			</div>
		<?php elseif($this->item->entry_type == 'ynultimatevideo'):?>
			<div class="contest_ynultimatevideo_view">
				<?php
						echo $this->item->content;
				?>
			</div>
		<?php else:?>
			<?php echo $this->item->content;?>
		<?php endif;?>
				
		<h3><?php echo $this->translate("Description")?></h3>
			<?php echo $this->item->summary;?>
		<div id="yncontest_entries_option" class="ynContest_entriesOption">		
			<span id="promote_id"> <?php echo $this->htmlLink(array(
				'route' => 'yncontest_general',
				'action' => 'promote-entry',
				'Id' => $this->item->getIdentity(),
				'format' => 'smoothbox'),
				$this->translate('Promote Entry'),
						array('class' => 'buttonlink smoothbox menu_yncontest_promote'));      ?>
			</span> |
			<?php if($this->viewer()->getIdentity()>0):?>
			<span id="share_id"> <?php echo $this->htmlLink(array(
				'module'=> 'activity',
				'controller' => 'index',
				'action' => 'share',
				'route' => 'default',
				'type' => $this->item->getType(),
				'id' => $this->item->getIdentity(),
				'format' => 'smoothbox'),
				$this->translate('Share'),
							array('class' => 'buttonlink smoothbox menu_yncontest_share'));      ?>
			</span>
			<?php endif;?>		
		</div>				
	</div>	
</div> 

