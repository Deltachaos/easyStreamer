<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class ZdfDeStream implements PluginEngine {

	const REGEX = '/^http:\/\/wstreaming.zdf.de\/zdf\/(.*)$/';

	public $stream;
	public $title = false;
	public $streamer;

	public function __construct($url, $curl, $plugin = null) {
		$this->stream = $url;
		if($plugin !== null) {
			$this->title = $plugin->title;
		}
		App::load('Mplayer', 'streamers');
		$this->streamer = new MplayerStreamer;
		$this->streamer->playlist = true;
	}

}


