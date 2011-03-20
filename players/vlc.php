<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class VlcPlayer implements PlayerEngine {
	
	public $binary = 'vlc';

	public function play($file) {
		$params = array(
			'tail',
			'-c',
			'+0',
			'-f',
			escapeshellarg($file),
			'|',
			$this->binary,
			'-'
		);
		$this->process = new Process('xterm', array(
			'-e',
			escapeshellarg('/bin/bash -c ' . escapeshellarg(implode(' ', $params)))
		), 'Player');
	}

}

