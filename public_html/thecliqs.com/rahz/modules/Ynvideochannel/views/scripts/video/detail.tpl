<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/jquery-1.10.2.min.js"></script>

<?php $video = $this -> video;
$videoId = $video->getIdentity(); ?>
<script type="text/javascript">
    jQuery.noConflict();
</script>

<div class="ynvideochannel_video_detail">
    <?php echo $this -> video -> getVideoIframe(620, 350);?>
    <div class="ynvideochannel_video_detail-title-rating clearfix">
        <div class="ynvideochannel_video_detail-title" style="width: calc(100% - 175px);">
            <?php echo htmlspecialchars($video->getTitle(), ENT_IGNORE, 'UTF-8') ?>
        </div>
        <div id="video_rating" class="ynvideochannel_video_detail-ratings ynvideochannel_videos_rating" onmouseout="rating_out();">
            <i id="rate_1" class="fa fa-star" <?php if ($this->viewer->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></i>
            <i id="rate_2" class="fa fa-star" <?php if ($this->viewer->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></i>
            <i id="rate_3" class="fa fa-star" <?php if ($this->viewer->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></i>
            <i id="rate_4" class="fa fa-star" <?php if ($this->viewer->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></i>
            <i id="rate_5" class="fa fa-star" <?php if ($this->viewer->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></i>
            <span id = "video_rating_score">
                <?php echo round($video->rating,2)?>
            </span>
            <span id="rating_text" class="ynvideochannel_video_detail-rating-text"></span>
        </div>
    </div>

    <div class="ynvideochannel_video_detail-info clearfix">
        <div class="ynvideochannel_video_detail-categories-owner clearfix">
            <div class="ynvideochannel_video_detail-owner">
                <?php
                    $poster = $video->getOwner();
                    echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array('class' => 'ynvideochannel_video_detail-owner-img clearfix'))
                ?>

                <div class="ynvideochannel_video_detail-owner-info">
                    <?php if($video->category_id) :?>
                        <span><?php echo $this->translate("Category") ?>:</span>
                        <?php echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $video)); ?>
                    <?php endif; ?>

                    <div class="ynvideochannel_video_detail-date">
                        <?php if ($this->channel && $this->channel->getIdentity()): ?>
                        <span><?php echo $this->translate("Channel") ?>:</span>
                        <?php echo $this->channel ?>
                        <?php endif; ?>
                    </div>

                    <div class="ynvideochannel_video_detail-owner-username">
                        <span><?php echo $this->translate('Posted by') ?></span>
                        <?php
                            $poster = $video->getOwner();
                            if ($poster) {
                                echo $this->htmlLink($poster, $poster->getTitle());
                            }
                        ?>
                        <span>&nbsp;.&nbsp;</span>
                        <span><?php echo $this->timestamp($video->creation_date) ?></span>
                    </div>
                </div>

            </div>
        </div>

        <div class="ynvideochannel_video_detail-count">
            <div class="ynvideochannel_video_detail-count-items">
                <div class="ynvideochannel_video_detail-count-item">
                    <?php echo $this->translate(array('<span>%s</span> favorite', '<span>%s</span> favorites', $video->favorite_count), $this->locale()->toNumber($video->favorite_count)) ?>
                </div>

                <div class="ynvideochannel_video_detail-count-item">
                    <?php echo $this->translate(array('<span>%s</span> view', '<span>%s</span> views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
                </div>

                <div class="ynvideochannel_video_detail-count-item">
                    <?php echo $this->translate(array('<span>%s</span> like', '<span>%s</span> likes', $video->like_count), $this->locale()->toNumber($video->like_count)) ?>
                </div>

                <div class="ynvideochannel_video_detail-count-item">
                    <?php echo $this->translate(array('<span>%s</span> comment', '<span>%s</span> comments', $video->comment_count), $this->locale()->toNumber($video->comment_count)) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ynvideochannel_video_detail-actions-description">
        <div class="ynvideochannel_video_detail-actions clearfix">
            <div class="ynvideochannel_addthis">
                <div class="addthis_sharing_toolbox"></div>
            </div>

            <div class="ynvideochannel_video_detail-actions-right">
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                    <?php echo $this->htmlLink(array(
                    'module'=>'activity',
                    'controller'=>'index',
                    'action'=>'share',
                    'route'=>'default',
                    'type'=>'ynvideochannel_video',
                    'id' => $videoId,
                    'format' => 'smoothbox'
                    ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynvideochannel_share_button smoothbox')); ?>
                    <?php $isLiked = $video->likes()->isLike($this->viewer()) ? 1 : 0; ?>
                    <a id="ynvideochannel_like_button" class="ynvideochannel_like_button <?php if( $isLiked ) echo 'ynvideochannel_liked' ?>" href="javascript:void(0);" onclick="onlike('<?php echo $video->getType() ?>', '<?php echo $videoId ?>', <?php echo $isLiked ?>);">
                        <?php if( $isLiked ): ?>
                        <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
                        <?php else: ?>
                        <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
                        <?php endif; ?>
                    </a>

                    <div class="ynvideochannel_video_detail-actions-mobilebox">
                        <div class="ynvideochannel_video_detail-addplaylist">
                            <?php echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $video)); ?>
                        </div>

                        <div class="ynvideochannel_channel_video_detail_more">
                            <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                            <span class="ynvideochannel_channel_video_detail_more-btn"><i class="fa fa-chevron-down"></i></span>

                            <ul class="ynvideochannel_channel_video_detail_more-explain">
                                <?php if(!$session -> mobile):?>
                                <li>
                                    <a href="javascript:void(0)" onclick="viewURL()"><i class="fa fa-link"></i><?php echo $this->translate('URL') ?></a>
                                </li>
                                <?php endif;?>
                                <li>
                                    <?php
                                    $url = $this->url(array(
                                    'module' => 'ynvideochannel',
                                    'controller' => 'video',
                                    'action' => 'embed',
                                    'id' => $videoId
                                    ),'default', true);
                                    ?>
                                    <a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><i class="fa fa-code"></i><?php echo $this->translate('HTML Code') ?></a>
                                </li>
                                <li>
                                    <?php echo $this->htmlLink(
                                    array(
                                    'route'=>'ynvideochannel_general',
                                    'action' => 'send-to-friends',
                                    'id' => $videoId,
                                    'type' => 'video'
                                    ),
                                    '<i class="fa fa-envelope"></i>'.$this->translate('Send to Friends'),
                                    array(
                                    'class' => 'smoothbox'
                                    )
                                    )?>
                                </li>
                                <?php
                                $url = $this->url(array(
                                'module' => 'core',
                                'controller' => 'report',
                                'action' => 'create',
                                'subject' => $video->getGuid()
                                ),'default', true);
                                ?>
                                <li>
                                    <a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><i class="fa fa-bolt"></i><?php echo $this->translate('Report') ?></a>
                                </li>

                                <li class="ynvideochannel_block " style="display:none">
                                    <form id="ynvideochannel_form_return_url" onsubmit="return false;">
                                        <span id="global_content_simple">
                                            <label class="ynvideochannel_popup_label"><?php echo $this->translate("URL")?></label>
                                            <input style="max-width: 100%" type="text" id="ynvideochannel_return_url" class="ynvideochannel_return_url"/>
                                            <br/>
                                            <div class="ynvideochannel_center" style="padding-top: 10px">
                                                <a href="javascript:void(0)" onclick="closeSmoothbox()" class="ynvideochannel_bold_link">
                                                    <button><?php echo $this->translate('Close')?></button>
                                                </a>
                                            </div>
                                        </span>
                                    </form>
                                </li>
                            </ul>
                            <?php endif ?>
                        </div>

                        <div class="ynvideochannel_video_detail-options">
                            <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $video)); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="ynvideochannel_video_detail-description-block">
            <?php if (count($this->videoTags)): ?>
                <div class="ynvideochannel_video_detail-tags">
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


            <div class="ynvideochannel_video_view_description ynvideochannel_video_show_less" id="ynvideochannel_video_detail_moreless">
                <div class="ynvideochannel_video_view_description-detail">
                    <div class="ynvideochannel_video_detail-descriptions">
                        <?php if($video->description):?>

                            <?php $description = $video->description;

                            $description = str_replace( "\r\n", "<br />", $description);
                            $description = str_replace( "\n", "<br />", $description);

                            echo $description; ?>

                        <?php endif;?>
                    </div>

                    <?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> video); ?>
                    <?php if($this -> fieldValueLoop($this -> video, $fieldStructure)):?>
                    <div class="ynvideochannel-profile-fields">
                        <div class="ynvideochannel-overview-title">
                            <span class="ynvideochannel-overview-title-content"><?php echo $this->translate('Video Specifications');?></span>
                        </div>
                        <div class="ynvideochannel-overview-content">
                            <?php echo $this -> fieldValueLoop($this -> video, $fieldStructure); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php if($video->description ):?>
        <div class="ynvideochannel_show_less_more" style="display: none;">
            <a href="javascript:void(0)" class="ynvideochannel_link_more">
                <?php echo $this->translate('View more') ?>
            </a>
        </div>
        <?php endif;?>
    </div>
</div>


<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynvideochannel.addthis.pubid', 'younet');?>" async="async"></script>
<script type="text/javascript">
    en4.core.runonce.add(function() {
        var pre_rate = <?php echo $video->rating; ?>;
        var video_id = <?php echo $video -> getIdentity(); ?>;
        var total_votes = <?php echo $video->rating_count?>;
        var viewer = <?php echo Engine_Api::_()->user()->getViewer()->getIdentity() ?>;
        var rating_over = window.rating_over = function(rating) {
                if( viewer == 0 ) {
                    $('rating_text').innerHTML = "<?php echo $this->translate('Please login to rate'); ?>";
                } else {
                    $('rating_text').innerHTML = "<?php echo $this->translate('Click to rate'); ?>";
                    for(var x=1; x<=5; x++) {
                    if(x <= rating) {
                    $('rate_'+x).set('class', 'fa fa-star');
                    } else {
                        $('rate_'+x).set('class', 'fa fa-star disable');
                    }
                }
            }
        }
        var rating_out = window.rating_out = function() {
            $('rating_text').innerHTML = "";
            if (pre_rate != 0){
            set_rating();
            }
            else {
                for(var x=1; x<=5; x++) {
                $('rate_'+x).set('class', 'fa fa-star disable');
                }
            }
        }
        var set_rating = window.set_rating = function() {
            var rating = pre_rate;
            for(var x=1; x<=parseInt(rating); x++) {
                $('rate_'+x).set('class', 'fa fa-star');
            }

            for(var x=parseInt(rating)+1; x<=5; x++) {
                $('rate_'+x).set('class', 'fa fa-star disable');
            }

            var remainder = Math.round(rating)-rating;
            if (remainder > 0){
                var last = parseInt(rating)+1;
                $('rate_'+last).set('class', 'fa fa-star-half-o');
            }
        }
        var rate = window.rate = function(rating) {
            $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
            (new Request.JSON({
                    'format': 'json',
                    'url' : '<?php echo $this->url(array('action' => 'rate', 'video_id' => $video -> getIdentity()), 'ynvideochannel_video', true) ?>',
                    'data' : {
                    'format' : 'json',
                    'rating' : rating,
                },
                'onRequest' : function(){
                },
                'onSuccess' : function(responseJSON, responseText)
                {
                    pre_rate = responseJSON.rating;
                    set_rating();
                    var video_rating_score = document.getElementById("video_rating_score");
                    video_rating_score.innerHTML = pre_rate;
                }
            })).send();

        }
        var tagAction = window.tagAction = function(tag){
            window.location = "<?php echo $this -> url(array('action' => 'browse-videos'), 'ynvideochannel_general', true);?>" + "?tag=" + tag;
        }
        set_rating();
        });

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
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button ynvideochannel_liked" href="javascript:void(0);" onclick="unlike(\'<?php echo $video->getType()?>\', \'<?php echo $videoId ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            },
            onComplete: function(responseJSON, responseText) {
            }
        }).send();
    }
</script>

<script type="text/javascript">
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
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $video->getType()?>\', \'<?php echo $videoId ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            }
        }).send();
    }
    function viewURL()
    {
        $('ynvideochannel_return_url').value = document.URL;
        if(window.innerWidth <= 408)
        {
            Smoothbox.open($('ynvideochannel_form_return_url'), {autoResize : true, width: 300});
        }
        else
        {
            Smoothbox.open($('ynvideochannel_form_return_url'));
        }
        var elements = document.getElements('video');
        elements.each(function(e)
        {
            e.style.display = 'none';
        });
    }

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

    ynvideochannelVideoOptions();
    //CHANNEL & VIDEO DETAIL MORE DROPDOWN
    $$('.ynvideochannel_channel_video_detail_more-btn').removeEvents('click').addEvent('click', function() {
        this.getParent('.ynvideochannel_channel_video_detail_more').toggleClass('explained');
    });

    $$('.ynvideochannel_channel_video_detail_more-btn').addEvent('outerClick',function(){
        var popup = this.getParent('.ynvideochannel_channel_video_detail_more');
        if (popup.hasClass('explained')){
            popup.removeClass('explained');
        }
    });


    //CHECK VIEWMORE DESCRIPTION
    var desc_h = $$('.ynvideochannel_video_detail-descriptions').getHeight();
    if(desc_h > 54){
        $$('.ynvideochannel_show_less_more').show();
    }

    jQuery('.ynvideochannel_show_less_more a').bind('click', function() {
        if (jQuery(this).hasClass('ynvideochannel_link_more')) {
            jQuery(this).html('<?php echo $this->translate('View less') ?>');
            jQuery(this).removeClass('ynvideochannel_link_more');
            jQuery(this).addClass('ynvideochannel_link_less');
            jQuery('#ynvideochannel_video_detail_moreless').removeClass('ynvideochannel_video_show_less');
        } else {
            jQuery(this).html('<?php echo $this->translate('View more') ?>');
            jQuery(this).addClass('ynvideochannel_link_more');
            jQuery(this).removeClass('ynvideochannel_link_less');
            jQuery('#ynvideochannel_video_detail_moreless').addClass('ynvideochannel_video_show_less');
        }
    });

</script>