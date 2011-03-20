<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class Streamer {

	public $url;
	public $destination;
	
	public function set($url, $destination) {
		$this->url = $url;
		$this->destination = $destination;
	}

	public function fillCache($cache) {
		$progressbar = Gui::progressbar(__('Fill Cache'));
		while(!file_exists($this->destination)) {
			clearstatcache();
			sleep(1);
		}
		$cache = ($cache * 1024);
		do {
			clearstatcache();
			$size = filesize($this->destination);
			$percent = 100 / $cache * $size;
			$progressbar->percent($percent);
			App::log(__('Cache: %s', $percent));
			usleep(150000);
		} while(ProcessHandler::isRunning('Streamer') && $size < $cache);
		$progressbar->close();
	}

}