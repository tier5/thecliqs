<?php
    $this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        var anchor = $('ynbusinesspages_music').getParent();
        $('ynbusinesspages_music_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynbusinesspages_music_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynbusinesspages_music_previous').removeEvents('click').addEvent('click', function(){
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

        $('ynbusinesspages_music_next').removeEvents('click').addEvent('click', function(){
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
                'type' => 'music',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
                 'tab' => $this->identity,
            ), '<i class="fa fa-list"></i>'.$this->translate('View all Playlists'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>
        <?php if ($this->canCreate):?>
            <?php echo $this->htmlLink(array(
                'route' => 'music_general',
                'module' => 'music',
                'action' => 'create',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
            ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create Playlist'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>
    </div>      

    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_playlist", "Music playlists", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<div class="ynbusinesspages_list" id="ynbusinesspages_music">
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
    
    <div class="ynbusinesspages-paginator">
        <div id="ynbusinesspages_music_previous" class="paginator_previous">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
            )); ?>
        </div>
        <div id="ynbusinesspages_music_next" class="paginator_next">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
            )); ?>
        </div>
    </div>
    
    <?php else: ?>
    <div class="tip">
        <span>
             <?php echo $this->translate('No playlists have been uploaded.');?>
        </span>
    </div>
    <?php endif; ?>
</div>