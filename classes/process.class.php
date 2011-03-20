<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Process {

	public $proc;
	public $stdin;
	public $stdout;
	public $stderr;
	public $type = false;

	public function __construct($binary = '', $params = array(), $type = false) {
		$this->type = $type;
		$cmd = $binary.' '.implode(' ', $params);
		App::log('Exec: '.$cmd, App::VERBOSE);
		$this->proc = proc_open($cmd, array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		), $pipes);
		$this->stdin = $pipes[0];
		$this->stdout = $pipes[1];
		$this->stderr = $pipes[2];
		ProcessHandler::add($this, $type);
	}

	public function isRunning() {
		$status = proc_get_status($this->proc);
		return $status['running'];
	}

	public function put($data) {
		return fputs($this->stdin, $data, strlen($data));
	}

	public function kill($force = false) {
		if(is_resource($this->stdin)) {
			fclose($this->stdin);
		}
		if(is_resource($this->stdout)) {
			fclose($this->stdout);
		}
		if(is_resource($this->stderr)) {
			fclose($this->stderr);
		}
		if(is_resource($this->proc)) {
			$return = proc_close($this->proc);
		} else {
			$return = true;
		}
		ProcessHandler::remove($this, $this->type);
		return $return;
	}

	public function __destruct() {
		$this->kill();
	}

}
