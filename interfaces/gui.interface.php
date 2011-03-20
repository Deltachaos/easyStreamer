<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

interface GuiProgressbarEngine {
	public function __construct($binary, $title);
	public function percent($percent = 0);
	public function close($force = false);
	public function isRunning();
}

interface GuiEngine {
	public function showmessage($msg = '');
	public function question($msg = '');
	public function progressbar($title = '');
	public function radiolist($title = '', $data = array(), $default = false);
	public function filebrowser($filename = '');
	public function input($title = '', $pre = '');
}

