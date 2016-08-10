<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<?php
    function full_url()  
    {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }
	$session = new Zend_Session_Namespace('mobile');
    $staticBaseUrl = $this->layout()->staticBaseUrl;
    $this->headLink()->appendStylesheet($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/mediaelementplayer/mediaelementplayer.css');
    $this->headScript()->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/mediaelementplayer/mediaelement-and-player.min.js');
    $video = $this->video;
    $videoId = $video->getIdentity();
?>

<?php
if (!$video || $video->status != 1):?>
<div class = 'tip'>
		<span>
   			<?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.'); ?>
   		</span>
</div>
<?php return; // Do no render the rest of the script in this mode
endif;
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        var pre_rate = <?php echo $video->rating; ?>;
        var rated = '<?php echo $this->rated; ?>';
        var video_id = <?php echo $video->video_id; ?>;
        var total_votes = <?php echo $this->rating_count; ?>;
        var viewer = <?php echo $this->viewer_id; ?>;

        var rating_over = window.rating_over = function(rating) {
            if( rated == 1 ) {
            $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
            //set_rating();
        } else if( viewer == 0 ) {
            $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
        } else {
            $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
            for(var x=1; x<=5; x++) {
            if(x <= rating) {
            $('rate_'+x).set('class', 'fa fa-star');
        } else {
            $('rate_'+x).set('class', 'fa fa-star-o');
        }
        }
        }
        }

        var rating_out = window.rating_out = function() {
            $('rating_text').innerHTML = "";
            //$('rating_text').innerHTML = en4.core.language.translate(['%s <?php echo $this -> translate('rating');?>', '%s <?php echo $this -> translate('ratings');?>', total_votes], total_votes);

            if (pre_rate != 0){
            set_rating();
        }
            else {
            for(var x=1; x<=5; x++) {
            $('rate_'+x).set('class', 'fa fa-star-o');
        }
        }
        }

        var set_rating = window.set_rating = function() {
            var rating = pre_rate;
            for(var x=1; x<=parseInt(rating); x++) {
            $('rate_'+x).set('class', 'fa fa-star');
        }

            for(var x=parseInt(rating)+1; x<=5; x++) {
            $('rate_'+x).set('class', 'fa fa-star-o');
        }

            var remainder = Math.round(rating)-rating;
            if (remainder <= 0.5 && remainder !=0){
            var last = parseInt(rating)+1;
            $('rate_'+last).set('class', 'fa fa-star-half-o');
        }
        }

        var rate = window.rate = function(rating) {
            $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
            for(var x=1; x<=5; x++) {
            $('rate_'+x).set('onclick', '');
        }
            (new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('action' => 'rate'), 'ynultimatevideo_general', true) ?>',
            'data' : {
            'format' : 'json',
            'rating' : rating,
            'video_id': video_id
        },
            'onRequest' : function(){
            rated = 1;
            total_votes = total_votes+1;
            pre_rate = (pre_rate+rating)/total_votes;
            set_rating();
        },
            'onSuccess' : function(responseJSON, responseText)
        {
            var total = responseJSON[0].total;
            total_votes = responseJSON[0].total;
        }
        })).send();

        }

        var tagAction = window.tagAction = function(tag){
            $('tag').value = tag;
            $('filter_form').submit();
        }

        set_rating();
        });
</script>

<div class="ynultimatevideo_detail">

    <form id="filter_form" class="global_form_box" method="post"
          action="<?php echo $this->url(array('action' => 'list'), 'ynultimatevideo_general', true) ?>" style='display:none;'>
        <input type="hidden" id="tag" name="tag" value=""/>
    </form>

    <div class="ynultimatevideo_detail_content">
        <?php if ($video->type == Ynultimatevideo_Plugin_Factory::getUploadedType() || $video->type == Ynultimatevideo_Plugin_Factory::getVideoURLType()): ?>
            <span class="view_html5_player">
                <video id="player_<?php echo $videoId; ?>" width="860" height="484" class="video-js vjs-default-skin" controls <?php if(!$session -> mobile):?>autoplay<?php endif;?>
                    preload="auto"  poster="<?php echo $this-> video -> getPhotoUrl("thumb.main");?>"
                         data-setup="{}">
                          <source src="<?php echo $this-> video_location;?>" type='video/mp4'>
                    </video>
    		</span>
        <?php else: ?>
        <div class="video_embed">
            <?php
               	 	echo $this->videoEmbedded;
            ?>
        </div>
        <?php endif; ?>

        <div class="ynultimatevideo_detail_title">
            <?php echo htmlspecialchars($video->getTitle()) ?>
        </div>

        <div class="ynultimatevideo_detail_block_info">
            <div class="ynultimatevideo_detail_categories_ratings_owner clearfix">

                <div class="ynultimatevideo_detail_owner">
                    <?php
                        $poster = $video->getOwner();
                        echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array('class' => 'ynultimatevideo_img_owner clearfix'))
                    ?>

                    <?php if ($video->category_id): ?>
                        <div class="ynultimatevideo_detail_categories">
                            <span><?php echo $this->translate("Category"); ?> : </span>
                            <?php $i = 0;  $category = $video->getCategory();  ?>
                            <?php if($category) :?>
                            <?php foreach($category->getBreadCrumNode() as $node): ?>
                            <?php if($node -> category_id != 1) :?>
                            <?php if($i != 0) :?>
                            &nbsp;<i class="fa fa-angle-right"></i>&nbsp;
                            <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
                            &nbsp;<i class="fa fa-angle-right"></i>&nbsp;
                            <?php endif;?>
                            <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
                            <?php endif;?>
                        </div>
                    <?php endif; ?>

                    <span class="ynultimatevideo_detail_owner_info ynultimatevideo_detail_owner_username">
                        <?php echo $this->translate('Posted by') ?>

                        <?php
                            $poster = $video->getOwner();
                        if ($poster) {
                        echo $this->htmlLink($poster, $poster->getTitle());
                        }
                        ?>
                    </span>
                    
                    <span class="ynultimatevideo_detail_owner_info">
                        <?php echo '&nbsp;.&nbsp;'.$this->timestamp($video->creation_date) ?>
                    </span>

                    <div id="video_rating" class="ynultimatevideo_detail_ratings ynultimatevideo_detail_owner_info" onmouseout="rating_out();">
                        <i id="rate_1" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></i>
                        <i id="rate_2" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></i>
                        <i id="rate_3" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></i>
                        <i id="rate_4" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></i>
                        <i id="rate_5" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></i>
                        <span id="rating_text" class="ynultimatevideo_rating_text"></span>
                    </div>
                </div>
            </div>

            <div class="ynultimatevideo_detail_button_count clearfix">

                <div class="ynultimatevideo_detail_count">


                    <div class="ynultimatevideo_detail_count_items">
                        <div class="ynultimatevideo_detail_count_item">
                            <?php echo $this->translate(array('<span>%s</span> favorite', '<span>%s</span> favorites', $video->favorite_count), $this->locale()->toNumber($video->favorite_count)) ?>
                        </div>

                        <div class="ynultimatevideo_detail_count_item">
                            <?php echo $this->translate(array('<span>%s</span> view', '<span>%s</span> views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
                        </div>

                        <div class="ynultimatevideo_detail_count_item">
                            <?php echo $this->translate(array('<span>%s</span> like', '<span>%s</span> likes', $video->like_count), $this->locale()->toNumber($video->like_count)) ?>
                        </div>

                        <div class="ynultimatevideo_detail_count_item">
                            <?php echo $this->translate(array('<span>%s</span> comment', '<span>%s</span> comments', $video->comment_count), $this->locale()->toNumber($video->comment_count)) ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="ynultimatevideo_detail_block_info_2">
            <div class="ynultimatevideo_detail_block_info_2_header clearfix">
                <div class="ynultimatevideo_addthis">
                    <div class="addthis_sharing_toolbox"></div>
                </div>

                <div class="ynultimatevideo_detail_block_button">
                    <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                        <?php echo $this->htmlLink(array(
                            'module'=>'activity',
                            'controller'=>'index',
                            'action'=>'share',
                            'route'=>'default',
                            'type'=>'ynultimatevideo_video',
                            'id' => $videoId,
                            'format' => 'smoothbox'
                        ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynultimatevideo_share_button smoothbox')); ?>
                        <?php $isLiked = $video->likes()->isLike($this->viewer()) ? 1 : 0; ?>
                        <a id="ynultimatevideo_like_button" class="ynultimatevideo_like_button" href="javascript:void(0);" onclick="onlike('<?php echo $video->getType() ?>', '<?php echo $videoId ?>', <?php echo $isLiked ?>);">
                            <?php if( $isLiked ): ?>
                            <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
                            <?php else: ?>
                            <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <?php if (Engine_Api::_()->ynultimatevideo()->canAddToPlaylist()): ?>
                    <div class="ynultimatevideo_button_add action-container">
                        <a class="ynultimatevideo_uix_button ynultimatevideo_add_button ynultimatevideo-action-link show-hide-btn" href="javascript:void(0)" title="<?php echo $this->translate('Add to playlist')?>">
                            <i class="fa fa-plus"></i>
                            <?php echo $this->translate('Add to') ?>
                        </a>
                        <div class="ynultimatevideo-action-pop-up" style="display: none">
                            <div class="add-to-playlist-notices"></div>
                            <?php
                                    $addedWatchlater = false;
                                    $watchlaterTable = Engine_Api::_() -> getDbTable('watchlaters', 'ynultimatevideo');
                                    if ($video -> getType() == 'ynultimatevideo_video'){
                                        $addedWatchlater = $watchlaterTable->isAdded($videoId, $this->viewer()->getIdentity());
                                    }
                                ?>
                                <div class="video-action-add-playlist">
                                    <div class="video-action-watch-later <?php echo ($addedWatchlater) ? 'added' : ''; ?>" onclick="ynultimatevideoAddToWatchLater(this, '<?php echo $video -> getIdentity() ?>');">
                                        <i class="fa fa-play-circle"></i>
                                        <?php echo $this->translate('Watch Later') ?>
                                </div>
                            </div>
                            <div class="video-action-add-playlist dropdow-action-add-playlist">
                                <span><?php echo $this-> translate('add to') ?></span>
                                <?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$video->getGuid()),'ynultimatevideo_playlist', true)?>
                                <div rel="<?php echo $url;?>" class="video-loading add-to-playlist-loading" style="display: none;text-align: center">
                                    <span class="ajax-loading">
                                        <img src='application/modules/Ynultimatevideo/externals/images/loading.gif'/>
                                    </span>
                                </div>
                                <div class="box-checkbox">
                                    <?php echo $this->partial('_add_exist_playlist.tpl', 'ynultimatevideo', array('item' => $video)); ?>
                                </div>
                            </div>

                                <?php if (Engine_Api::_()->ynultimatevideo()->canCreatePlaylist()): ?>
                                <div class="video-action-dropdown ynultimatevideo-action-dropdown">
                                    <a href="javascript:void(0);" onclick="ynultimatevideoAddNewPlaylist(this, '<?php echo $video->getGuid()?>');" class="ynultimatevideo-action-link add-to-playlist" data="<?php echo $video->getGuid()?>"><i class="fa fa-plus"></i><span><?php echo $this->translate('Add to new playlist')?></span></a>
                                    <span class="play_list_span"></span>

                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php endif;?>

                    <div class="ynultimatevideo_button_more">
                        <a href="javascript:void(0)" class="ynultimatevideo_button_more_btn"><?php echo $this->translate('More').'&nbsp;<i class="fa fa-angle-down"></i>' ?></a>
                        <ul class="ynultimatevideo_button_more_explain">
                            <?php if(!$session -> mobile):?>
                                <li>
                                   <i class="fa fa-link"></i><a href="javascript:void(0)" onclick="viewURL()"><?php echo $this->translate('URL') ?></a>
                                </li>
                            <?php endif;?>
                            <?php if ($this->can_embed): ?>
                                <li>
                                <?php
                                    $url = $this->url(array(
                                            'module' => 'ynultimatevideo',
                                            'controller' => 'video',
                                            'action' => 'embed',
                                            'id' => $videoId
                                    ),'default', true);
                                ?>
                                <i class="fa fa-code"></i><a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate('HTML Code') ?></a>
                                </li>
                            <?php endif ?>
                            <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                                <li>
                                    <i class="fa fa-envelope"></i>
                                    <?php echo $this->htmlLink(
                                        array(
                                            'route' => 'ynultimatevideo_specific',
                                            'action' => 'send-to-friends',
                                            'id' => $videoId
                                        ),
                                        $this->translate('Send to Friends'),
                                        array(
                                            'class' => 'smoothbox'
                                        )
                                    )?>
                                </li>
                            <?php endif; ?>
                            <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
    
                            <?php
                                $url = $this->url(array(
                                    'module' => 'core',
                                    'controller' => 'report',
                                    'action' => 'create',
                                    'subject' => $video->getGuid()
                                ),'default', true);
                            ?>
                                <li>
                                    <i class="fa fa-bolt"></i><a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><?php echo $this->translate('Report') ?></a>
                                </li>
                            <?php if ($this->can_edit): ?>
                                <li>
                                    <i class="fa fa-pencil"></i>
                                        <?php
                                    echo $this->htmlLink(array(
                                        'route' => 'ynultimatevideo_general',
                                        'action' => 'edit',
                                        'video_id' => $video->video_id
                                            ), $this->translate('Edit Video'));
                                    ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->can_delete && $video->status != 2): ?>
                                <li>
                                    <i class="fa fa-trash"></i>
                                        <?php
                                    echo $this->htmlLink(array(
                                            'route' => 'ynultimatevideo_general',
                                            'action' => 'delete',
                                            'video_id' => $video->video_id,
                                            'format' => 'smoothbox'
                                        ),
                                        $this->translate('Delete Video'),
                                        array(
                                            'class' => 'smoothbox'
                                        ));
                                    ?>
                                </li>
                            <?php endif; ?>
                            <?php endif ?>

                            <li class="ynultimatevideo_block " style="display:none">
                                <form id="ynultimatevideo_form_return_url" onsubmit="return false;">
                                    <span id="global_content_simple">
                                        <label class="ynultimatevideo_popup_label"><?php echo $this->translate("URL")?></label>
                                        <input style="max-width: 100%" type="text" id="ynultimatevideo_return_url" class="ynultimatevideo_return_url"/>
                                        <br/>
                                        <div class="ynultimatevideo_center" style="padding-top: 10px">
                                            <a href="javascript:void(0)" onclick="closeSmoothbox()" class="ynultimatevideo_bold_link">
                                                <button><?php echo $this->translate('Close')?></button>
                                            </a>
                                        </div>
                                    </span>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> video); ?>
            <?php if($video->description || $this -> fieldValueLoop($this -> video, $fieldStructure) || count($this->videoTags)):?>
            <div class="ynultimatevideo_detail_block_info_2_content">
                <?php if (count($this->videoTags)): ?>
                <div class="ynultimatevideo_detail_tags">
                    <span>
                        <i class="fa fa-tag"></i>&nbsp;<?php echo $this->translate('Tags:') ?>
                    </span>

                    <?php foreach ($this->videoTags as $index => $tag): ?>
                    <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>
                        <?php echo $tag->getTag()->text ?>
                    </a>
                    <?php if ($index < count($this->videoTags) - 1) : ?>
                    ,&nbsp;
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="ynultimatevideo_video_view_description ynultimatevideo_video_show_less" id="ynultimatevideo_video">
                    <div class="ynultimatevideo_detail_description">
                        <div class="ynultimatevideo_detail_description_video">
                            <?php if($video->description):?>
                                <div class="ynultimatevideo_detail_description_video_title">
                                    <?php echo $this->translate('Video Description') ?>
                                </div>
                                <?php $description = $video->description;
                                      $description = str_replace( "\r\n", "<br />", $description);
                                      $description = str_replace( "\n", "<br />", $description);
                                      echo $description; ?>
                            <?php endif;?>
                        </div>

                        <?php if($this -> fieldValueLoop($this -> video, $fieldStructure)):?>
                            <div class="ynultimatevideo-profile-fields">
                                <div class="ynultimatevideo-overview-title ynbusinesspages-overview-line">
                                    <span class="ynultimatevideo-overview-title-content"><?php echo $this->translate('Video Specifications');?></span>
                                </div>
                                <div class="ynultimatevideo-overview-content">
                                    <?php echo $this -> fieldValueLoop($this -> video, $fieldStructure); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($video->description || $video->category_id || count($this->videoTags)):?>
            <div class="ynultimatevideo_show_less_more" style="display: none;">
                <a href="javascript:void(0)" class="ynultimatevideo_link_more">
                    <?php echo $this->translate('View more') ?>
                </a>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynultimatevideo.addthis.pubid', 'younet');?>" async="async"></script>

<?php if ($video->type == Ynultimatevideo_Plugin_Factory::getUploadedType() || $video->type == Ynultimatevideo_Plugin_Factory::getVideoURLType()): ?>
    <script type="text/javascript">
        var currentVideoId = <?php echo $videoId; ?>;
        jQuery(document).ready(function($) {
            jQuery('#player_' + currentVideoId).mediaelementplayer({
                success: function (mediaElement) {
                }
            });
        });
    </script>
<?php endif; ?>

<script language="javascript" type="text/javascript">
    window.addEvent('domready', function(){
        $$('a.ynultimatevideo_button_more_btn').removeEvents('click').addEvent('click', function() {

            var parent = this.getParent('.ynultimatevideo_button_more');
            var popup = parent.getElement('.ynultimatevideo_button_more_explain');

            var pageParent = this.getParent('.layout_middle');
            var otherPopup = pageParent.getElement('.ynultimatevideo-action-pop-up');
            if (otherPopup != null) {
                otherPopup.hide();
            }

            popup.toggle();

            var layout_parent = popup.getParent('.layout_middle');
            if (!layout_parent) layout_parent = popup.getParent('#global_content');
            if (layout_parent.hasClass('popup-padding-bottom')) {
                layout_parent.setStyle('padding-bottom', '0');
            }
            var y_position = popup.getPosition(layout_parent).y;
            var p_height = layout_parent.getHeight();
            var c_height = popup.getHeight();
            if (popup.isDisplayed()) {
                if(p_height - y_position < (c_height + 1)) {
                    layout_parent.addClass('popup-padding-bottom');
                    layout_parent.setStyle('padding-bottom', (c_height + 1 + y_position - p_height)+'px');
                }
                else if (layout_parent.hasClass('popup-padding-bottom')) {
                    layout_parent.setStyle('padding-bottom', '0');
                }
            }
            else {
                if (layout_parent.hasClass('popup-padding-bottom')) {
                    layout_parent.setStyle('padding-bottom', '0');
                }
            }
        });
    });

    jQuery('.ynultimatevideo_show_less_more a').bind('click', function() {
        if (jQuery(this).hasClass('ynultimatevideo_link_more')) {
            jQuery(this).html('<?php echo $this->translate('View less') ?>');
            jQuery(this).removeClass('ynultimatevideo_link_more');
            jQuery(this).addClass('ynultimatevideo_link_less');
            jQuery('#ynultimatevideo_video').removeClass('ynultimatevideo_video_show_less');
        } else {
            jQuery(this).html('<?php echo $this->translate('View more') ?>');
            jQuery(this).addClass('ynultimatevideo_link_more');
            jQuery(this).removeClass('ynultimatevideo_link_less');
            jQuery('#ynultimatevideo_video').addClass('ynultimatevideo_video_show_less');
        }
    });

    function closeSmoothbox()
    {
        var block = Smoothbox.instance;
        block.close();
        var elements = document.getElements('video');
        elements.each(function(e)
        {
            e.style.display = 'block';
        });
    }

    function viewURL()
    {
        jQuery('#ynultimatevideo_return_url').val(document.URL);
        if(window.innerWidth <= 408)
        {
            Smoothbox.open($('ynultimatevideo_form_return_url'), {autoResize : true, width: 300});
        }
        else
        {
            Smoothbox.open($('ynultimatevideo_form_return_url'));
        }
        var elements = document.getElements('video');
        elements.each(function(e)
        {
            e.style.display = 'none';
        });
    }

    function openPopup(url)
    {
        if(window.innerWidth <= 480)
        {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else
        {
            Smoothbox.open(url);
        }
    }

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

    function onlike(itemType, itemId, isLiked) {
        if (isLiked) {
            unlike(itemType, itemId);
        } else {
            like(itemType, itemId);
        }
    }

    function like(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="ynultimatevideo_like_button" class="ynultimatevideo_like_button" href="javascript:void(0);" onclick="unlike(\'<?php echo $video->getType()?>\', \'<?php echo $videoId ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
                    $("ynultimatevideo_like_button").outerHTML = html;
                }
            },
            onComplete: function(responseJSON, responseText) {
            }
        }).send();
    }

    function unlike(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="ynultimatevideo_like_button" class="ynultimatevideo_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $video->getType()?>\', \'<?php echo $videoId ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
                    $("ynultimatevideo_like_button").outerHTML = html;
                }
            }
        }).send();
    }

    var desc_h = $$('.ynultimatevideo_video_view_description').getHeight();
    if(desc_h > 36){
        $$('.ynultimatevideo_show_less_more').show();
    }

</script>