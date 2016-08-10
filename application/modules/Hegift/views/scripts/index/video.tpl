<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: video.tpl  24.02.12 20:16 TeaJay $
 * @author     Taalay
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
?>
<script type='text/javascript'>
  en4.core.runonce.add(function() {
    flashembed("gift_video_embed", {
      src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.1.5.swf",
      width: 500,
      height: 360,
      wmode: 'transparent'
    }, {
      config: {
        clip: {
          url: "<?php echo $this->video_location;?>",
          autoPlay: false,
          autoBuffering: true
        },
        plugins: {
          controls: {
            background: '#000000',
            bufferColor: '#333333',
            progressColor: '#444444',
            buttonColor: '#444444',
            buttonOverColor: '#666666'
          }
        },
        canvas: {
          backgroundColor:'#000000'
        }
      }
    });
  });
</script>

<?php if (!isset($this->message)) : ?>
  <div id="gift_video_embed" class="gift_on_smoothbox"></div>
<?php else : ?>
  <?php echo $this->translate($this->message);?>
<?php endif; ?>
