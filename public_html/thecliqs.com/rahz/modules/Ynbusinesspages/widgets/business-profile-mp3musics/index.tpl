<?php
    $this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        var anchor = $('ynbusinesspages_mp3music').getParent();
        $('ynbusinesspages_mp3music_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynbusinesspages_mp3music_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynbusinesspages_mp3music_previous').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                }
            }), {
                'element' : anchor
            })
        });

        $('ynbusinesspages_mp3music_next').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                }
            }), {
                'element' : anchor
            })
        });
    });
</script>

<div class="ynbusinesspages-profile-module-header">
    <!-- Menu Bar -->
    <div class="ynbusinesspages-profile-header-right">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynbusinesspages_extended',
                'controller' => 'music',
                'action' => 'list',
                'type' => 'mp3music',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
                 'tab' => $this->identity,
            ), '<i class="fa fa-list"></i>'.$this->translate('View all Albums'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>

		<?php if ($this->canCreate):?>
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
    </div>      

    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_mp3music", "Mp3music albums", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<div class="ynbusinesspages_list" id="ynbusinesspages_mp3music">
	<!-- Content -->
	<?php if( $this->paginator->getTotalItemCount() > 0 ): 
	$business = $this->business;?>
	<ul class="thumbs ynbusinesspages_music">  			
		<?php foreach ($this->paginator as $album): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($album);?>     	
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
		</li>
		<?php endforeach; ?> 		 
	</ul>  
	
	<div class="ynbusinesspages-paginator">
        <div id="ynbusinesspages_mp3music_previous" class="paginator_previous">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
            )); ?>
        </div>
        <div id="ynbusinesspages_mp3music_next" class="paginator_next">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
            )); ?>
        </div>
    </div>
	
	<?php else: ?>
	<div class="tip">
		<span>
		  <?php echo $this->translate('No albums have been uploaded.');?>
		</span>
	</div>
	<?php endif; ?>
</div>
  