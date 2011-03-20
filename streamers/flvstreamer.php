<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class FlvstreamerStreamer extends Streamer implements StreamerEngine {
	
	public $process;
	public $binary = 'flvstreamer';
	public $server = false;

	public function download() {
		$params = array();
		if(is_string($this->server)) {
			$params[] = '-r';
			$params[] = escapeshellarg($this->server);
			$params[] = '--playpath';
			$path = substr($this->url, strlen($this->server));
			$params[] = escapeshellarg($path);
		} else {
			$params[] = '-r';
			$params[] = escapeshellarg($this->url);
		}
		if(file_exists($this->destination)) {
			unlink($this->destination);
		}
		$this->process = new Process($this->binary.' '.implode(' ', $params), array(
			'>',
			escapeshellarg($this->destination)
		), 'Streamer');
	}

}

