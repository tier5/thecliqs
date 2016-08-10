<div id="ynmusic-playlists-listing-widget" class="music-listing">

    <div class="ynmusic-block-count-mode-view <?php if (empty($this->mode_enabled)) echo 'not-mode-view'?>">
    	<div id="ynmusic-total-item-count"><?php echo $this->translate(array('ynmusic_playlist_count_num_ucf', '%s Playlists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></div>
    	
    	<div id="ynmusic-view-mode-<?php echo $this->identity;?>" class="ynmusic-modeview-button">
    		<?php if(in_array('list', $this -> mode_enabled)):?>
            <span class="" rel="ynmusic_list-view" title="<?php echo $this->translate('List View')?>"><i class="fa fa-th-list"></i></span>
            <?php endif;?>
            <?php if(in_array('grid', $this -> mode_enabled)):?>
            <span class="" rel="ynmusic_grid-view" title="<?php echo $this->translate('Grid View')?>"><i class="fa fa-th"></i></span>
            <?php endif;?>
    	</div>
    </div>

	
	<div id="ynmusic-listing-content-<?php echo $this ->identity;?>" class="ynmusic-listing-content">
		<?php echo $this->partial('_playlist-listing.tpl', 'ynmusic', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'paging' => true));?>
	</div>
</div>

<script type="text/javascript">
en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});
var mode_enabled<?php echo $this->identity?> = [];
<?php foreach ($this->mode_enabled as $mode) :?>
mode_enabled<?php echo $this->identity?>.push('ynmusic_<?php echo $mode?>-view');
<?php endforeach;?>
if (mode_enabled<?php echo $this->identity?>.length == 0) {
	mode_enabled<?php echo $this->identity?>.push('ynmusic_list-view');
}

window.addEvent('domready', function(){
	var myCookieViewMode = getCookie('ynmusic-listing-modeview-<?php echo $this -> identity; ?>');
    if ( myCookieViewMode == '') {
        myCookieViewMode = '<?php echo $this->class_mode?>';
    }
    
    if (mode_enabled<?php echo $this->identity?>.indexOf(myCookieViewMode) == -1) {
    	myCookieViewMode = mode_enabled<?php echo $this->identity?>[0];
    }
    
    $$('#ynmusic-view-mode-<?php echo $this -> identity;?> > span[rel='+myCookieViewMode+']').addClass('active');
    $$('#ynmusic-listing-content-<?php echo $this -> identity; ?>').addClass(myCookieViewMode);
    
    // Set click viewMode
    $$('#ynmusic-view-mode-<?php echo $this -> identity;?> > span').addEvent('click', function(){
        var viewmode = this.get('rel');
        var content = $('ynmusic-listing-content-<?php echo $this -> identity; ?>');

        setCookie('ynmusic-listing-modeview-<?php echo $this -> identity; ?>', viewmode, 1);

        // set class active
        $$('#ynmusic-view-mode-<?php echo $this->identity;?> > span').removeClass('active');
        this.addClass('active');

        content
            .removeClass('ynmusic_list-view')
            .removeClass('ynmusic_grid-view');

        content.addClass( viewmode );
    });
});
</script>