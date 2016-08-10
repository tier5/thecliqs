<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<?php if ($this->error == 1): ?>
    <?php echo $this->translate('Embedding of videos has been disabled.') ?>
    <?php return ?>
<?php elseif ($this->error == 2): ?>
    <?php echo $this->translate('Embedding of videos has been disabled for this video.') ?>
    <?php return ?>
<?php elseif (!$this->video || $this->video->status != 1): ?>
    <?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.') ?>
    <?php return ?>
<?php endif; ?>
<script type="text/javascript">
    var pre_rate = <?php echo $this->video->rating; ?>;
    var video_id = <?php echo $this->video->video_id; ?>;
    var total_votes = <?php echo $this->rating_count; ?>;
  
    function set_rating() {
        var rating = pre_rate;
        $('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
        for(var x=1; x<=parseInt(rating); x++) {
            $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
        }

        for(var x=parseInt(rating)+1; x<=5; x++) {
            $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
        }

        var remainder = Math.round(rating)-rating;
        if (remainder <= 0.5 && remainder !=0){
            var last = parseInt(rating)+1;
            $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
        }
    }
    en4.core.runonce.add(set_rating);
</script>

<h2>
    <?php echo htmlspecialchars ($this->video->getTitle()) ?>
</h2>

<div class='video_view_container' style="max-width: 500px;">
    <div class="video_view video_view_container">
        <?php if ($this->video->type == 3): ?>
            <video id="my_video" class="video-js vjs-default-skin" controls <?php if(!$session -> mobile):?>autoplay<?php endif;?>
            preload="auto"  poster="<?php echo $this-> video -> getPhotoUrl("thumb.main");?>"
            data-setup="{}">
                <source src="<?php echo $this-> video_location;?>" type='video/mp4'>
            </video>
        <?php else: ?>
            <div class="video_embed">
                <?php echo $this->videoEmbedded; ?>
            </div>
        <?php endif; ?>
        <div class="video_date">
            <?php
                echo $this->translate('Posted by %1$s on %2$s', 
                    $this->htmlLink($this->video->getOwner(), htmlspecialchars ($this->video->getOwner()->getTitle())),
                    $this->timestamp($this->video->creation_date));
            ?>
            <?php if ($this->category): ?>
                - <?php echo $this->translate('Filed in') ?>
                <?php
                echo $this->htmlLink(array(
                    'route' => 'ynultimatevideo_general',
                    'QUERY' => array('category' => $this->category->category_id)
                        ), $this->category->category_name
                )
                ?>
            <?php endif; ?>
            <?php if (count($this->videoTags)): ?>
                -
                <?php foreach ($this->videoTags as $tag): ?>
                    <a href='javascript:void(0);'>#<?php echo $tag->getTag()->text ?></a>&nbsp;
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div id="video_rating" class="rating">
            <span id="rate_1" class="rating_star_big_generic"></span>
            <span id="rate_2" class="rating_star_big_generic"></span>
            <span id="rate_3" class="rating_star_big_generic"></span>
            <span id="rate_4" class="rating_star_big_generic"></span>
            <span id="rate_5" class="rating_star_big_generic"></span>
            <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate') ?></span>
        </div>
        <div class="video_desc" style="max-height: 55px;">
            <?php echo $this->video->description; ?>
        </div>
    </div>
</div>