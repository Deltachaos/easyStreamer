<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Plugin {

	public $curl;
	public $plugin = false;

	public $title = false;
	public $stream = false;
	public $streamer = false;
	public $cache = array(
		153600,
		102400,
		71680,
		51200,
		20480 => true,
		10240,
		8192
	);

	public function __construct($url = false, $curl = null, $plugin = null) {
		if($curl === null) {
			$this->curl = new CurlLib;
		} else {
			$this->curl = $curl;
		}
		if($plugin !== null) {
			$this->plugin = $plugin;
		}
		if($url !== false) {
			$plugins = array();
			$d = dir(PATH.DS.'plugins');
			while(($entry = $d->read()) !== false) {
				if(substr($entry, 0, 1) !== '.') {
					$p = dir($d->path.DS.$entry);
					while(($file = $p->read()) !== false) {
						if(substr($file, 0, 1) !== '.') {
							$file = substr($file, 0, strrpos($file, '.'));
							$file = Inflector::camelize($file);
							App::load($file, 'plugins'.DS.$entry);
							$plugins[] = Inflector::camelize($entry).$file;
						}
					}
					$p->close();
				}
			}
			$d->close();
			$pluginclass = false;
			foreach($plugins as $plugin) {
				$class = Inflector::camelize($plugin);
				if(defined($class.'::REGEX')) {
					if(preg_match($class::REGEX, $url)) {
						$pluginclass = $class;
						break;
					}
				}
			}
		}
		if($pluginclass === false) {
			Gui::showmessage(__('Cannot find plugin'));
			$this->stream = $url;
			App::load('Mplayer', 'streamers');
			$this->streamer = new MplayerStreamer;
		} else {
			App::log(__('Loading Plugin: "%s"', $plugin));
			$plugin = new $pluginclass($url, $this->curl, $this->plugin);
			$this->setMeta($plugin);
			if(is_array($plugin->stream)) {
				if(($url = Gui::radiolist(__('Choose Stream'), $plugin->stream)) === false) {
					die;
				}
				if(method_exists($plugin, 'stream')) {
					$plugin->stream($url);
				}
				$plugin = new Plugin($url, $this->curl, $plugin);
				$this->setMeta($plugin);
				$this->plugin = $plugin;
			}
		}
	}

	public function setMeta($plugin) {
		if(isset($plugin->title)) {
			$this->title = $plugin->title;
		}
		if(isset($plugin->streamer)) {
			$this->streamer = $plugin->streamer;
		}
		if(isset($plugin->cache)) {
			$this->cache = $plugin->cache;
		}
		if(isset($plugin->stream)) {
			$this->stream = $plugin->stream;
		}
	}

	public function getPlayer($player) {
		$player = Inflector::camelize($player);
		App::load($player, 'players');
		$player = Inflector::camelize($player).'Player';
		return new $player;
	}

	public function select() {
		if(is_array($this->cache)) {
			if(count($this->cache) == 1) {
				$this->cache = array_values($this->cache);
				$this->cache = $this->cache[0];
			} else {
				$cache = array();
				$num = false;
				foreach($this->cache as $key => $value) {
					if(!is_bool($value)) {
						$key = $value;
						$value = false;
					}
					$cache[$key] = array(__('Cache') => $key);
					if($value) {
						$num = $key;
					}
				}
				ksort($cache);
				$this->cache = Gui::radiolist(__('Choose Name'), $cache, $num);
			}
		}
		if(is_array($this->title)) {
			if(count($this->title) == 1) {
				$this->title = array_keys($this->title);
				$this->title = $this->title[0];
			} else {
				$this->title = Gui::radiolist(__('Choose Name'), $this->title);
			}
		}
		if($this->title === false) {
			if(($pos = strrpos($this->stream, '/')) !== false) {
				$this->title = substr($this->stream, $pos);
			} else {
				$this->title = $this->stream;
			}
		}
		if(($file = Gui::filebrowser($this->title)) === false) {
			die;
		}
		return $file;
	}

}
