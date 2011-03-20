<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class StdinGuiProgressbarEngine implements GuiProgressbarEngine {

	public $process;

	public function  __construct($binary, $title) {
	}

	public function percent($percent = 0) {
		$this->percent = $percent;
		return true;
	}

	public function isRunning() {
		return $this->percent < 100;
	}

	public function close($force = false) {
		return true;
	}

}

class StdinGuiEngine implements GuiEngine {

	public function showmessage($msg = '') {
		echo $msg . "\n";
	}

	public function question($msg = '') {
		$return = $this->input($msg.' [y/n]');
		if($return == 'y') {
			return true;
		} else {
			return false;
		}
	}

	public function progressbar($title = '') {
		return new StdinGuiProgressbarEngine(false, $title);
	}

	public function radiolist($title = '', $data = array(), $default = false) {

		$colums = array();
		foreach($data as $col) {
			$colums = array_merge($colums, array_flip(array_keys($col)));
		}
		$colums = array_keys($colums);
		foreach($colums as $col) {
			$list[] = $col;
		}

		$keys = array();
		$i = 0;
		$tmp = implode(' ', $colums).":";
		foreach($data as $key => $cols) {
			$active = false;
			if(is_bool($default)) {
				if($default === false) {
					$default = $active = true;
					$default = $i;
				}
			}

			$keys[$i] = $key;
			$tmp .= "\n".'['.$i.']';
			foreach($colums as $col) {
				$str = ' ';
				if(isset($cols[$col])) {
					$str .= $cols[$col];
				}
				$tmp .= $str;
			}
			$i++;
		}
		echo $tmp;
		echo "\n";
		echo $title.": ";

		$return = '';
		$handle = fopen('php://stdin', 'r');
		while(strpos($return, "\n") === false) {
			$return .= fread($handle, 10);
		}
		fclose($handle);
		$return = substr($return, 0, strpos($return, "\n"));
		if(strlen($return) > 0) {
			return $keys[$return];
		} else {
			return $keys[$default];
		}
	}

	public function filebrowser($filename = '') {
		$result = $this->input(__('Choose File'), getcwd().DS.$filename);
		return $result;
	}

	public function input($title = '', $pre = '') {
		echo __('Predefined Text: ');
		echo $pre."\n";
		echo __('If you want to use this keep the field clean');
		echo "\n";
		echo $title.": ";
		$return = '';
		$handle = fopen('php://stdin', 'r');
		while(strpos($return, "\n") === false) {
			$return .= fread($handle, 10);
		}
		fclose($handle);
		$return = substr($return, 0, strpos($return, "\n"));
		if(strlen($return) > 0) {
			return $return;
		} else {
			return $pre;
		}
	}

}

