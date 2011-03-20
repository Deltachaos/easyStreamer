<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class ZenityGuiProgressbarEngine implements GuiProgressbarEngine {

	public $process;

	public function  __construct($binary, $title) {
		$this->process = new Process($binary, array(
			'--progress',
			'--title='.escapeshellarg($title),
			'--percentage=0',
			'--auto-close',
			'--width=400'
		), 'ZenityProgressbar');
	}

	public function percent($percent = 0) {
		return $this->process->put($percent."\n");
	}

	public function isRunning() {
		return $this->process->isRunning();
	}

	public function close($force = false) {
		return $this->process->kill($force);
	}

}

class ZenityGuiEngine implements GuiEngine {

	public $binary = 'zenity';

	public function showmessage($msg = '') {
		exec(utf8_decode($this->binary.' '.implode(' ', array(
			'--info',
			'--text',
			escapeshellarg($msg)
		))));
	}

	public function question($msg = '') {
		$return = exec(utf8_decode($this->binary.' '.implode(' ', array(
			'--question',
			'--text',
			escapeshellarg($msg),
			';echo $?'
		))));
		return !(bool)(int)$return;
	}

	public function progressbar($title = '') {
		return new ZenityGuiProgressbarEngine($this->binary, $title);
	}

	public function radiolist($title = '', $data = array(), $default = false) {
		$list = array(
			'--list',
			'--text',
			escapeshellarg($title),
			'--radiolist',
			'--width=600',
			'--height=400',
			'--hide-column=2',
			'--print-column=2',
			'--column',
			'""',
			'--column',
			'""'
		);
		$colums = array();
		foreach($data as $col) {
			$colums = array_merge($colums, array_flip(array_keys($col)));
		}
		$colums = array_keys($colums);
		foreach($colums as $col) {
			$list[] = '--column';
			$list[] = escapeshellarg($col);
		}
		foreach($data as $key => $cols) {
			$active = false;
			if(is_bool($default)) {
				if(!$default) {
					$default = $active = true;
				}
			} else if($key == $default) {
				$active = true;
			}
			$list[] = $active ? 'TRUE' : 'FALSE';
			$list[] = escapeshellarg($key);
			foreach($colums as $col) {
				$str = '';
				if(isset($cols[$col])) {
					$str = $cols[$col];
				}
				$list[] = escapeshellarg($str);
			}
		}
		$return = exec(utf8_decode($this->binary.' '.implode(' ', $list)));
		if(strlen($return) > 0) {
			return $return;
		} else {
			return false;
		}
	}

	public function filebrowser($filename = '') {
		$return = exec($this->binary.' '.implode(' ', array(
			'--file-selection',
			'--save',
			'--confirm-overwrite',
			'--filename',
			escapeshellarg($filename)
		)));
		if(strlen($return) > 0) {
			return $return;
		} else {
			return false;
		}
	}

	public function input($title = '', $pre = '') {
		$return = exec(utf8_decode($this->binary.' '.implode(' ', array(
			'--entry',
			'--text',
			escapeshellarg($title),
			'--entry-text',
			escapeshellarg($pre)
		))));
		if(strlen($return) > 0) {
			return $return;
		} else {
			return false;
		}
	}

}

