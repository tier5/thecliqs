<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<script type="text/javascript">    
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
        var anchor = $('ynultimatevideo_list_item_browse_<?php echo $this->identity; ?>').getParent();
            $('ynultimatevideo_fav_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynultimatevideo_fav_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynultimatevideo_fav_videos_previous').removeEvents('click').addEvent('click', function(){
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

            $('ynultimatevideo_fav_videos_next').removeEvents('click').addEvent('click', function(){
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
        <?php endif; ?>
    });
</script>

<div id="ynultimatevideo_list_item_browse_<?php echo $this->identity; ?>" class="<?php echo $this -> class_mode;?>">
    <div id="ynultimatevideo_tabs_browse" class="ynultimatevideo_nomargin clearfix">
        <div class="ynultimatevideo-action-view-method clearfix">
            <?php if(in_array('simple', $this -> mode_enabled)):?>
            <div class="ynultimatevideo_home_page_list_content">
                <span id="simple_view_<?php echo $this->identity;?>" class="ynultimatevideo_home_page_list_content_icon tab_icon_simple_view" title="<?php echo $this->translate('Simple View')?>" onclick="ynultimatevideoUpdateViewMode(<?php echo $this->identity ?>, 'simple');"><i class="fa fa-th-large"></i></span>
            </div>
            <?php endif;?>

            <?php if(in_array('list', $this -> mode_enabled)):?>
            <div class="ynultimatevideo_home_page_list_content">
                <span id="list_view_<?php echo $this->identity;?>" class="ynultimatevideo_home_page_list_content_icon tab_icon_list_view" title="<?php echo $this->translate('List View')?>" onclick="ynultimatevideoUpdateViewMode(<?php echo $this->identity ?>, 'list');"><i class="fa fa-th-list"></i></span>
            </div>
            <?php endif;?>

            <?php if(in_array('casual', $this -> mode_enabled)):?>
            <div class="ynultimatevideo_home_page_list_content">
                <span id="casual_view_<?php echo $this->identity;?>" class="ynultimatevideo_home_page_list_content_icon tab_icon_casual_view" title="<?php echo $this->translate('Casual View')?>" onclick="ynultimatevideoUpdateViewMode(<?php echo $this->identity ?>, 'casual');"><i class="fa fa-align-center"></i></span>
            </div>
            <?php endif;?>
        </div>
    </div>

    <div id="ynultimatevideo_list_item_browse_content" class="ynultimatevideo_list_item_browse_content_listgrid">
        <?php
                echo $this->partial('_list_most_item.tpl', 'ynultimatevideo', array('videos' => $this->paginator, 'tab' => 'videos_browse_video'));
        ?>
    </div>

    <div id="ynultimatevideo_list_item_browse_content" class="ynultimatevideo_list_item_browse_content_casual">
        <?php
            echo $this->partial('_list_casual_item.tpl', 'ynultimatevideo', array('videos' => $this->paginator, 'tab' => 'videos_browse_video'));
        ?>
    </div>
</div>

<div>
    <div id="ynultimatevideo_fav_videos_previous" class="paginator_previous">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="ynultimatevideo_fav_videos_next" class="paginator_next">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => '',
            'class' => 'buttonlink_right icon_next'
        ));
        ?>
    </div>
</div>

<script type="text/javascript">

    function ynultimatevideoAddNewPlaylist(ele, guid) {
        var nextEle = ele.getNext();
        if(nextEle.hasClass("ynultimatevideo_active_add_playlist")) {
            //click to close
            nextEle.removeClass("ynultimatevideo_active_add_playlist");
            nextEle.setStyle("display", "none");
        } else {
            //click to open
            nextEle.addClass("ynultimatevideo_active_add_playlist");
            nextEle.setStyle("display", "block");
        }
        $$('.play_list_span').each(function(el){
            if(el === nextEle){
                //do not empty the current box
            } else {
                el.empty();
                el.setStyle("display", "none");
                el.removeClass("ynultimatevideo_active_add_playlist");
            }
        });
        var data = guid;
        var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynultimatevideo_playlist', true);?>';
        var request = new Request.HTML({
            url : url,
            data : {
                subject: data,
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                var spanEle = nextEle;
                spanEle.innerHTML = responseHTML;
                eval(responseJavaScript);

                var popup = spanEle.getParent('.ynultimatevideo-action-pop-up');
                var layout_parent = popup.getParent('.layout_middle');
                if (!layout_parent) layout_parent = popup.getParent('#global_content');
                var y_position = popup.getPosition(layout_parent).y;
                var p_height = layout_parent.getHeight();
                var c_height = popup.getHeight();
                if(p_height - y_position < (c_height + 21)) {
                    layout_parent.addClass('popup-padding-bottom');
                    var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                    layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
                }
            }
        });
        request.send();
    }

    function ynultimatevideoAddToPlaylist(ele, playlistId, guild) {
        var checked = ele.get('checked');
        var data = guild;
        var url = '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynultimatevideo_playlist', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                subject: data,
                playlist_id: playlistId,
                checked: checked,
            },
            onSuccess: function(responseJSON) {
                if (!responseJSON.status) {
                    ele.set('checked', !checked);
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

    function ynultimatevideoAddToWatchLater(ele, video_id) {
        var url = '<?php echo $this->url(array('action' => 'add-to'), 'ynultimatevideo_watch_later', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                video_id: video_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.added == 1) {
                        var html = '<i class="fa fa-ban"></i>' + ' ' + '<?php echo $this->translate('Unwatched') ?>';
                        ele.innerHTML = html;
                    } else {
                        var html = '<i class="fa fa-play-circle"></i>' + ' ' + '<?php echo $this->translate('Watch Later') ?>';
                        ele.innerHTML = html;
                    }
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

    function ynultimatevideoAddToFavorite(ele, video_id) {
        var url = '<?php echo $this->url(array('action' => 'add-to'), 'ynultimatevideo_favorite', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                video_id: video_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.added == 1) {
                        ele.addClass('added');
                    } else {
                        ele.removeClass('added');
                    }
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

</script>