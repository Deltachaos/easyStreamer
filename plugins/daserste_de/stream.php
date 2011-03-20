<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class DasersteDeStream implements PluginEngine {

	const REGEX = '/^rtmp:\/\/vod.daserste.de\/ardfs\/(.*)$/';

	public $stream;
	public $title = false;
	public $streamer;

	public function __construct($url, $curl, $plugin = null) {
		$this->stream = $url;
		if($plugin !== null) {
			$this->title = $plugin->title;
		}
		App::load('Flvstreamer', 'streamers');
		$this->streamer = new FlvstreamerStreamer;
		$this->streamer->server = $plugin->server[$url];
		$this->cache = array(
			1192
		);
	}

}


