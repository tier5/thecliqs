<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>
<?php
    $channel = $this->channel;
    $videos = $channel -> getVideos();
?>
<input name="order" type="hidden" id="videos-order" value=""/>
<input name="deleted" type="hidden" id="videos-deleted" value=""/>
<div class="clearfix" style="margin-bottom: 10px;">
    <input type='checkbox' class='checkbox' id = 'video_selectall'/>
    <label for="video_selectall" class="optional"><?php echo $this -> translate("Apply channel privacy for all videos");?></label>
</div>
<?php if(count($videos) > 0): ?>

<div class="ynvideochannel_channel-edit-subdragdrop"><?php echo $this-> translate('Drag & drop to reorder videos in channel') ?></div>

<div id="channel-video-items">
    <?php foreach ($videos as $key => $video): ?>
    <div id="<?php echo $video -> getIdentity();?>" class="ynvideochannel-video-item">
        <span class="video-move-handle"><i class="fa fa-bars"></i> <span><?php echo $key + 1; ?>. </span>&nbsp;<?php echo $video->getTitle();?></span>
        <span class="ynvideochannel_privacy_video clearfix">
            <input type='checkbox' name="videos[]" id="video_<?php echo $video->getIdentity()?>" value="<?php echo $video->getIdentity()?>"/>
            <label for="video_<?php echo $video->getIdentity()?>" class="optional"><?php echo $this -> translate("Apply channel privacy");?></label>
        </span>
        <span class="channel-video-remove"><i class="fa fa-times"></i><?php echo $this -> translate("Remove");?></span>
    </div>
    <?php endforeach; ?>
</div>
<?php endif ?>
<script type="text/javascript">

    en4.core.runonce.add(function(){
        new Sortables('channel-video-items', {
            contrain: false,
            clone: true,
            handle: 'div.ynvideochannel-video-item',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                var order = this.serialize().toString();
                $('videos-order').set('value', order);
            }
        });

        $('video_selectall').addEvent('click', function(){
            var checked  = $(this).checked;
            var checkboxes = $$('input[type=checkbox][name=videos[]]');
            checkboxes.each(function(item){
                item.checked = checked;
            });
        })


    });

    window.addEvent('domready', function() {
        $$('.channel-video-remove').addEvent('click', function() {
            var parent = this.getParent('.ynvideochannel-video-item');
            var id = parent.get('id');
            var ids = $('videos-deleted').get('value');
            if (ids == '') ids = id;
            else ids = ids+','+id;
            $('videos-deleted').set('value', ids);
            parent.destroy();
        });
    });
</script>