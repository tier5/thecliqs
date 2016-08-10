
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php
$this->headScript()
      ->appendFile('application/modules/Hecore/externals/scripts/imagezoom/core.js');
 $this->headLink()
      ->appendStylesheet($this->layout()->staticBaseUrl . 'application/css.php?request=application/modules/Hecore/externals/styles/imagezoom/core.css');

?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var myElements = $$('.product-item');
    var i = 0;
    for(i=0; i<myElements.length; i++){
      var photo = myElements[i].getProperty('id');
      initImageZoom(
        {
          rel: photo,
          transition: Fx.Transitions.Cubic.easeIn
        }
      );
    }
  });
</script>

<div class="layout_middle">
	<span class="product-item" id="thumbs-photo">
  <?php foreach( $this->paginator as $key => $photo ): ?>
		<div class="store_thumbs_nocaptions" style="height: 160px;">
			<table>
				<tr valign="middle">
					<td valign="middle" height="160" width="140">
            <div id="photo_<?php echo $key+1; ?>" class="center">
              <a style="text-align: left;" rel="thumbs-photo[<?php echo $this->product->getTitle()?>]" title="<?php echo ($photo->title) ? '<b>'.$photo->title.'</b>: '.$photo->description : ''; ?>" href="<?php echo $photo->getPhotoUrl(); ?>">
								<img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" class="thumbs">
              </a>
            </div>
					</td>
				</tr>
			</table>
		</div>
  <?php endforeach; ?>
	</span>
</div>
<br />