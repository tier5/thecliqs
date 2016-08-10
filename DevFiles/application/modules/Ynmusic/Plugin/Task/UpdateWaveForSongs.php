<?php
class Ynmusic_Plugin_Task_UpdateWaveForSongs extends Core_Plugin_Task_Abstract {
	public function execute() {
		$output = array();
		$result = -1;
		exec('`/usr/bin/which lame` --help 2>&1', $output, $result);
		
		if ($result != 0) {
			return;
		}
		
		$table = Engine_Api::_()->getItemTable('ynmusic_song');
		$select = $table->select()->where('update_wave = ?', 0);
		$songs = $table->fetchAll($select);
		foreach ($songs as $song) {
			require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/SongWave.php';
			$waveParh = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.'waves.png';
			$play_top = '#00d6ff';
			$play_bot = '#80ebff';
			$noplay_top = '#666666';
			$noplay_bot = '#b3b3b3';
			$params = array(
	            'parent_type' => 'ynmusic_song',
	            'parent_id' => $song->getIdentity(),
	            'user_id' => $song->user_id
	        );
			$waveApi = new SongWave();
			$storage = Engine_Api::_() -> storage();
			$waveApi->createWavePhoto($song->getFilePath(), $play_top, $play_bot);
	        $aMain = $storage -> create($waveParh, $params);
	        $song->wave_play = $aMain -> file_id;
			$waveApi->createWavePhoto($song->getFilePath(), $noplay_top, $noplay_bot);
	        $aMain = $storage -> create($waveParh, $params);
	        $song->wave_noplay = $aMain -> file_id;
			$song->update_wave = 1;
			$song->save();
		}		
	}
}