<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class App {

	const VERBOSE = 0;
	const NOTICE = 0;
	const WARNING = 0;
	const ERROR = 0;

	public static $settings = array(
		'gui' => 'zenity',
		'player' => 'mplayer'
	);

	public static function translate($msg) {
		return $msg;
	}

	public static function log($msg = '', $level = self::NOTICE) {
		echo $msg . "\n";
	}

	public static function load($file = '', $folder = null) {
		if(strlen($folder) > 0) {
			$folder = DS . $folder;
		}
		$typ = '';
		if(($pos = strpos($file, '.')) !== false) {
			$typ = substr($file, $pos);
			$file = substr($file, 0, $pos);
		}
		$file = Inflector::underscore($file);
		$path = PATH . $folder . DS . $file . $typ . '.php';
		self::log(__('Loading File "%s"', $file.$typ.'.php'), self::VERBOSE);
		require_once $path;
	}

	public static function parseSettings() {
		$settings = $_SERVER['argv'];
		$lastkey = null;
		$key = null;
		foreach($settings as $setting) {
			$str = ltrim($setting, '-');
			if(substr($setting, 0, 1) == '-') {
				$key = $lastkey = $str;
				$value = true;
			} else {
				$key = $lastkey;
				$value = $str;
			}
			if($key !== null) {
				if(isset(self::$settings[$key]) && is_array(self::$settings[$key])) {
					self::$settings[$key][] = $value;
				} else {
					self::$settings[$key] = $value;
				}
			}
		}
	}

	public static function is($key = '') {
		$parts = array();
		if(($pos = strpos('.', $key)) !== false) {
			$parts[] = substr($key, 0, $pos);
			$parts[] = substr($key, $pos + 1);
		} else {
			$parts[] = $key;
		}
		if(count($parts) > 1) {
			if(isset(self::$settings[$parts[0]][$parts[1]])) {
				return self::$settings[$parts[0]][$parts[1]];
			} else {
				return false;
			}
		} else {
			if(isset(self::$settings[$parts[0]])) {
				return self::$settings[$parts[0]];
			} else {
				return false;
			}
		}
	}

	public static function exists($binary = '') {
		$which = `which $binary`;
		if(!empty($which)) {
			return $which;
		} else {
			return false;
		}
	}

}

