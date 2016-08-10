<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: manage-video.tpl  22.02.12 18:21 TeaJay $
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

<h2>
  <?php echo $this->translate('Virtual Gifts Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("HEGIFT_VIEWS_SCRIPTS_ADMININDEX_MANAGEVIDEO_DESCRIPTION") ?>
</p>
<br />

<?php echo $this->render('_adminGiftOptions.tpl')?>

<div class="admin_home_middle">
  <?php if( !$this->gift || $this->gift->status != 1 ): ?>
    <ul class="form-errors">
      <li>
        <?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.');?>
      </li>
    </ul>
  <?php else : ?>
    <?php if (!empty($this->video_location)) : ?>
      <div id="gift_video_embed" class="gift_video_embed"></div>
    <?php endif; ?>
    <div class="settings">
      <?php echo $this->form->render($this)?>
    </div>
  <?php endif; ?>
</div>