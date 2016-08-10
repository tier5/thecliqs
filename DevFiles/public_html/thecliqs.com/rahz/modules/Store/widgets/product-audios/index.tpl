<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-09-14 17:07:11 taalay $
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

<div class="store-audio-view">
	<div class="store-audio-view-songs">
		<table cellpadding="0" cellspacing="0" class="tracklist">
			<thead>
				<tr class="thead">
					<td class="number" width="1%">#</td>
					<td class="title"><?php echo $this->translate("Track"); ?></td>
					<td class="play" width="5%"><?php echo $this->translate("Play"); ?></td>
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
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>