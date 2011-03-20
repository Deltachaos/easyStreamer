<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class MplayerStreamer extends Streamer implements StreamerEngine {

	public $process;
	public $binary = 'mplayer';
	public $playlist = false;
	
	public function download() {
		$params = array();
		if($this->playlist) {
			$params[] = '-playlist';
		}
		$params[] = escapeshellarg($this->url);
		$params[] = '-dumpstream';
		$params[] = '-dumpfile';
		$params[] = escapeshellarg($this->destination);
		if(file_exists($this->destination)) {
			unlink($this->destination);
		}
		
		$this->process = new Process($this->binary, $params, 'Streamer');
	}

}


