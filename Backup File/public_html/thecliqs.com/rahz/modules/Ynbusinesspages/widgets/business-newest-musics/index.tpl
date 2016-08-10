<!-- Content -->
<?php if( $this->paginator->getTotalItemCount() > 0 ): 
$business = $this->business;?>
<ul class="thumbs ynbusinesspages_music">           
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
                        <?php $owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($playlist); 
                        echo $this->htmlLink($owner, $owner ->getTitle()) ?>
                    </div>                          
                </div>
            </div>
        </div>          
    </li>         
    <?php endforeach; ?>             
</ul>  
<?php endif; ?>