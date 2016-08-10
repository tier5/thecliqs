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
    $staticBaseUrl = $this->layout()->staticBaseUrl;
    $this->headScript()
    ->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js')
    ->appendScript('jQuery.noConflict();')
    ->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/vendor/jquery.ui.widget.js')
    ->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.iframe-transport.js')
    ->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.fileupload.js')
    ->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');
?>

<?php
    $playlist = $this->playlist;
    $videos = $playlist -> getVideos();
?>
<input name="order" type="hidden" id="videos-order" value=""/>
<input name="deleted" type="hidden" id="videos-deleted" value=""/>
<div id="playlist-video-items">
    <?php foreach ($videos as $key => $video): ?>
        <div id="<?php echo $video -> getIdentity();?>" class="ynultimatevideo-video-item">
            <span class="video-move-handle"><i class="fa fa-bars"></i> <span><?php echo $key + 1; ?>. </span>&nbsp;<?php echo $video->getTitle();?></span>
            <span class="playlist-video-remove"><i class="fa fa-times"></i>Remove</span>
        </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">

    en4.core.runonce.add(function(){
        new Sortables('playlist-video-items', {
            contrain: false,
            clone: true,
            handle: 'div.ynultimatevideo-video-item',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                var order = this.serialize().toString();
                $('videos-order').set('value', order);
            }
        });
    });

    window.addEvent('domready', function() {
        $$('.playlist-video-remove').addEvent('click', function() {
            var parent = this.getParent('.ynultimatevideo-video-item');
            var id = parent.get('id');
            var ids = $('videos-deleted').get('value');
            if (ids == '') ids = id;
            else ids = ids+','+id;
            $('videos-deleted').set('value', ids);
            parent.destroy();
        });
    });
</script>