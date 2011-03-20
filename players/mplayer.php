<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class MplayerPlayer implements PlayerEngine {
	
	public $binary = 'mplayer';

	public function play($file) {
		$params = array(
			'tail',
			'-c',
			'+0',
			'-f',
			escapeshellarg($file),
			'|',
			$this->binary,
			'-cache',
			'8192',
			'-'
		);
		$this->process = new Process('xterm', array(
			'-e',
			escapeshellarg('/bin/bash -c ' . escapeshellarg(implode(' ', $params)))
		), 'Player');
	}

}

