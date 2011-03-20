<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class ProcessHandler {

	protected static $objects = array();

	public static function add($process, $type = 0) {
		if(!isset(self::$objects[$type])) {
			self::$objects[$type] = array();
		}
		if(in_array($process, self::$objects[$type])) {
			return false;
		}
		self::$objects[$type][] = $process;
		return true;
	}

	public static function remove($process, $type = 0) {
		if(isset(self::$objects[$type]) && ($key = array_search($process, self::$objects[$type], true))) {
			unset(self::$objects[$type][$key]);
			return true;
		}
		return false;
	}

	public static function isRunning($type = 0) {
		if(isset(self::$objects[$type])) {
			foreach(self::$objects[$type] as $obj) {
				if($obj->isRunning()) {
					return true;
				}
			}
		}
		return false;
	}

	public static function killall($type = 0) {
		if(!isset(self::$objects[$type])) {
			return false;
		}
		$return = true;
		foreach(self::$objects[$type] as $object) {
			$return = $return && $object->kill();
		}
		return $return;
	}

}
