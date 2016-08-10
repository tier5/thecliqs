<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<div class="headline">
			<h2>
				<?php echo $this->business->__toString();
					echo $this->translate('&#187; Albums Music');
				?>
			</h2>
		</div>
	</div>
</div>
<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="album_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle">
		<div class="generic_layout_container">
		<!-- Menu Bar -->
		<div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
					'class' => 'buttonlink'
					)) ?>
				
				<?php if ($this->canCreate):?>
					<?php if ($this->ItemTable == 'music_playlist'): ?>
						<?php echo $this->htmlLink(array(
						'route' => 'music_general',
						'module' => 'music',
						'controller' => 'index',
						'action' => 'create',
						'business_id' => $this->business->getIdentity(),
						'parent_type' => 'ynbusinesspages_business',
						), '<i class="fa fa-plus-square"></i>'.$this->translate('Create Playlist'), array(
						'class' => 'buttonlink'
						))
						?>
					<?php else: ?> 	
						<?php echo $this->htmlLink(array(
						'route' => 'mp3music_create_album',
						'module' => 'mp3music',
						'controller' => 'album',
						'action' => 'create',
						'business_id' => $this->business->getIdentity(),
	                    'parent_type' => 'ynbusinesspages_business',
						), '<i class="fa fa-plus-square"></i>'.$this->translate('Create Album'), array(
						'class' => 'buttonlink'
						))?>
					<?php endif; ?>
				<?php endif; ?>      
            </div>      

            <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>			
            <div class="ynbusinesspages-profile-header-content">
	            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
	            <?php if ($this->ItemTable == 'music_playlist'): ?>
	            	<?php echo $this-> translate(array("ynbusiness_playlist", "Music playlists", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
				<?php else : ?>
					<?php echo $this-> translate(array("ynbusiness_mp3music", "Mp3music albums", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
				<?php endif; ?>
            </div>
            <?php endif; ?>
        </div>  

		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>

		<ul class="thumbs ynbusinesspages_music">  	
		<?php if ($this->ItemTable == 'music_playlist'): ?>  		
			<?php foreach ($this->paginator as $playlist): ?>
			<li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
				<div class="music_browse_info">
					<div class="photo">
						<?php if($playlist -> getPhotoUrl("thumb.profile")): ?>
							<span class="image-thumb" style="background-image:url('<?php echo $playlist -> getPhotoUrl("thumb.profile"); ?>')"></span>
						<?php else: ?>
							<span class="image-thumb" style="background-image:url('<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynbusinesspages/externals/images/nophoto_music_playlist.png')"></span>
						<?php endif; ?>
					</div>
					<div class="info">
						<div class="music_browse_info_title title">
							<?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
						</div>
						<div class="stats">
							<div class="author-name">
								<?php 
								$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($playlist);
								echo $this->htmlLink($owner, $owner->getTitle()) ?>
							</div>							
						</div>
					</div>
                    
				</div>	        
				<div class="ynbusinesspages-profile-module-option">
                    <?php 
                    $canRemove = $business -> isAllowed('music_delete', null, $playlist);
                    $canDelete = $playlist->isDeletable();
                    $canEdit = $playlist->isEditable();
                    ?>
                    <?php if ($canRemove || $canDelete || $canEdit): ?>
                    <?php if ($canEdit): ?>
                          <?php echo $this->htmlLink(
                          $playlist->getHref(
                            array('route' => 'music_playlist_specific', 
                                'action' => 'edit', 
                                'business_id' => $this->subject()->getIdentity(),
                                'parent_type' => 'ynbusinesspages_business')),
                                '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Playlist'),
                            array('class'=>'buttonlink'))
                          ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'default',
                            'module' => 'music',
                            'controller' => 'playlist',
                            'action' => 'delete',
                            'playlist_id' => $playlist->getIdentity(),
                            'format' => 'smoothbox',
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Playlist'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?> 
                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $playlist->getIdentity(),
                            'item_type' => 'music_playlist',
                            'item_label' => 'Playlist',
                            'remove_action' => 'music_delete',
                            'business_id' => $business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Playlist To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                    <?php endif; ?>
                </div>
			</li>	      
			<?php endforeach; ?>
		<?php else: ?>	
			<?php foreach ($this->paginator as $album): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($album); ?>     	
			<li id="mp3music_album_item_<?php echo $album->getIdentity() ?>">
				<div class="mp3music_browse_info music_browse_info">
					<div class="photo">
						<a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)">
							<?php if($album -> getPhotoUrl("thumb.profile")): ?>
								<span class="image-thumb" style="background-image:url('<?php echo $album -> getPhotoUrl("thumb.profile"); ?>')"></span>
							<?php else: ?>
								<span class="image-thumb" style="background-image:url('<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynbusinesspages/externals/images/nophoto_music_playlist.png')"></span>
							<?php endif; ?>
						 </a> 
					</div>
					<div class="info">
						<div class="mp3music_browse_info_title title">					
						<?php if($album->getSongIDFirst($album->album_id)): ?>
							<a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)"><?php echo $album->getTitle() ?></a>
						<?php else: ?>
							<?php echo $album->getTitle() ?>
						<?php endif; ?>					
						</div>
						<div class="stats">
							<div class="author-name">
							<?php if(Engine_Api::_() -> ynbusinesspages() -> getSingers($album->album_id)): ?>
								<?php echo Engine_Api::_() -> ynbusinesspages() -> getSingers($album->album_id);?>
							<?php else: ?>
								<?php echo $this->htmlLink($owner, $owner->getTitle()) ?>
							<?php endif; ?>
							</div>						
						</div>                        
					</div>                    
				</div>
				<div class="ynbusinesspages-profile-module-option">
                        <?php 
                        $canRemove = $business -> isAllowed('music_delete', null, $album);
                        $canDelete = $album->isDeletable();
                        $canEdit = $album->isEditable();
                        ?>
                        <?php if ($canRemove || $canDelete || $canEdit): ?>
                        <?php 
                            $params = array(
                                'business_id' => $this->business->getIdentity(),
                                'parent_type' => 'ynbusinesspages_business',
                            ) ;
                        ?>
                        <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink($album->getEditHref($params),
                            '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit'),
                            array('class'=>'buttonlink'
                            )) ?>
                        <?php endif; ?>
                        <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink($album->getDeleteHref($params),
                            '<i class="fa fa-trash-o"></i>'.$this->translate('Delete'),
                            array('class'=>'buttonlink smoothbox'
                        )) ?>
                        <?php endif; ?>
                        <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $album->getIdentity(),
                            'item_type' => 'mp3music_album',
                            'item_label' => 'Album',
                            'remove_action' => 'music_delete',
                            'business_id' => $business->getIdentity(),
                        ),'<i class="fa fa-times"></i>'.$this->translate('Delete Album To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                <?php endif; ?>
                </div>
			</li>
			<?php endforeach; ?>  
		<?php endif; ?>	
		</ul>  
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
		    <?php $label = ($this->itemTable == 'music_playlist') ? $this->translate('Playlists') : $this->translate('Albums');?>
			<span>
			  <?php echo $this->translate('No %s have been uploaded.', $label);?>
			</span>
		</div>
		<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>
  