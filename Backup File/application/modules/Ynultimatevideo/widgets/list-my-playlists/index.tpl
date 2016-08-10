<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<div id="ynultimatevideo-list-playlists-widget">
    <div id="ynultimatevideo-listing-content-<?php echo $this ->identity;?>" class="ynultimatevideo-listing-content">

    <div class="ynultimatevideo_count_videos">
        <div class="ynultimatevideo-total-item-count">
            <i class="fa fa-file-text"></i>
            <?php echo $this->translate(array('%1$s playlist', '%1$s playlists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?>
        </div>

        <div id="ynultimatevideo_tabs_browse" class="ynultimatevideo_nomargin clearfix">
            <div id="ynultimatevideo-view-mode-<?php echo $this->identity;?>" class="ynultimatevideo-modeview-button">
            
                <?php if(in_array('list', $this -> mode_enabled)):?>
                <div class="ynultimatevideo_home_page_list_content">
                    <span class="ynultimatevideo_home_page_list_content_icon tab_icon_list_view" rel="ynultimatevideo_list-view" title="<?php echo $this->translate('List View')?>"><i class="fa fa-th-list"></i></span>
                </div>
                <?php endif;?>

                <?php if(in_array('grid', $this -> mode_enabled)):?>
                <div class="ynultimatevideo_home_page_list_content">
                    <span class="ynultimatevideo_home_page_list_content_icon tab_icon_grid_view" rel="ynultimatevideo_grid-view" title="<?php echo $this->translate('Grid View')?>"><i class="fa fa-th"></i></span>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>

        <?php echo $this->partial('_playlist-listing.tpl', 'ynultimatevideo', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'paging' => true));?>
    </div>
</div>

<script type="text/javascript">
    var mode_enabled<?php echo $this->identity?> = [];

    <?php foreach ($this->mode_enabled as $mode) :?>
    mode_enabled<?php echo $this->identity?>.push('ynultimatevideo_<?php echo $mode?>-view');
    <?php endforeach;?>
    if (mode_enabled<?php echo $this->identity?>.length == 0) {
        mode_enabled<?php echo $this->identity?>.push('ynultimatevideo_list-view');
    }

    window.addEvent('domready', function(){
        var myCookieViewMode = getCookie('ynultimatevideo-listing-modeview-<?php echo $this -> identity; ?>');
        if ( myCookieViewMode == '') {
            myCookieViewMode = '<?php echo $this->class_mode?>';
        }

        if (mode_enabled<?php echo $this->identity?>.indexOf(myCookieViewMode) == -1) {
            myCookieViewMode = mode_enabled<?php echo $this->identity?>[0];
        }

        $$('#ynultimatevideo-view-mode-<?php echo $this -> identity;?> span[rel='+myCookieViewMode+']').addClass('active');
        $$('#ynultimatevideo-listing-content-<?php echo $this -> identity; ?>').addClass(myCookieViewMode);

        // Set click viewMode
        $$('#ynultimatevideo-view-mode-<?php echo $this -> identity;?> span').addEvent('click', function(){
            var viewmode = this.get('rel');
            var content = $('ynultimatevideo-listing-content-<?php echo $this -> identity; ?>');

            setCookie('ynultimatevideo-listing-modeview-<?php echo $this -> identity; ?>', viewmode, 1);

            // set class active
            $$('#ynultimatevideo-view-mode-<?php echo $this->identity;?> span').removeClass('active');
            this.addClass('active');

            content
                    .removeClass('ynultimatevideo_list-view')
                    .removeClass('ynultimatevideo_grid-view');

            content.addClass( viewmode );
        });
    });

    $$('.ynultimatevideo_options_btn').addEvent('click',function(){
        this.getParent('.ynultimatevideo_options_block').getElement('.ynultimatevideo_options').toggle();
    })
</script>