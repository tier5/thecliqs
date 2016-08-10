<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl  19.09.11 14:09 TeaJay $
 * @author     Taalay
 */
?>


<?php
	$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile( $this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
		->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Store/externals/standalone/audio-player.js');
?>

<script type="text/javascript">

  AudioPlayer.setup("<?php echo $this->layout()->staticBaseUrl ?>application/modules/Store/externals/standalone/player.swf", {
		width: 290,
		initialvolume: 100,
		transparentpagebg: "yes",
		left: "c49c86",
		lefticon: "c49c86"
	});
</script>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity(),'product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
            'class' => 'buttonlink product_back',
            'id' => 'store_product_editsettings',
          )) ?>
          <br>
        <?php if (count($this->audios)) : ?>
          <?php echo $this->htmlLink($this->url(array('controller' => 'audios', 'action' => 'create', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('Add Audios'), array(
              'class' => 'buttonlink product_audios_new',
              'id' => 'store_product_addaudios',
            )) ?>
            <br>
        <?php endif; ?>
      </div>
    </li>
  </ul>
</div>

<h2>
  <?php echo $this->translate('Edit Audios') . ' - ' . $this->htmlLink($this->product->getHref(), $this->product->getTitle()); ?>
</h2>

<div class="store-audio-view">
	<div class="store-audio-view-songs">
		<table cellpadding="0" cellspacing="0" class="tracklist">
			<thead>
				<tr class="thead">
					<td class="number" width="1%">#</td>
					<td class="title"><?php echo $this->translate("Track"); ?></td>
					<td class="play" width="5%"><?php echo $this->translate("Play"); ?></td>
          <td class="delete"><?php echo $this->translate("Delete"); ?></td>
        </tr>
			</thead>
			<tbody>
				<?php $number = 0; ?>
				<?php foreach($this->audios as $song) :?>
				<?php $number++; ?>
				<tr class="song">
					<td class="number" width="1%"><span class="misc_info"><?php echo $number; ?>.</span></td>
					<td class="title"><?php echo $song->getTitle(); ?></td>
					<td class="listen" width="290px">
						<div id="song_wrapper_<?php echo $song->getIdentity(); ?>">
							<div id="song_<?php echo $song->getIdentity(); ?>"></div>
						</div>
						<script type="text/javascript">
							AudioPlayer.embed("song_<?php echo $song->getIdentity(); ?>", {soundFile: "<?php echo $this->storage->get($song->file_id)->map(); ?>", titles: "<?php echo $song->getTitle(); ?>"});
						</script>
					</td>
          <td class="delete">
            <?php echo $this->htmlLink(
              $this->url(array(
                     'controller' => 'audios',
                     'action' => 'delete',
                     'product_id' => $this->product->getIdentity(),
                     'audio_id' => $song->getIdentity()
                   ),
              'store_extended', true),
              '<img title="'.$this->translate('Delete').'" src="application/modules/Store/externals/images/audio_delete.png">',
              array('class' => 'smoothbox'));
            ?>
          </td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>