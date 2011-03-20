<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


/*
Gui::showmessage('sdfjhskjfhskf');
print_r(Gui::radiolist('ashaskd', array(
	array(
		'collum' => '1value',
		'collum2' => '1value2'
	),
	array(
		'collum' => '2value',
		'collum2' => '2value2'
	),
	array(
		'collum' => '2value',
		'collum2' => '2value2',
		'coll' => 'sfsf'
	)
)));
print_r(Gui::filebrowser('abc.asd'));
print_r(Gui::input('abc.asd'));

$progress = Gui::progressbar('sdfjhskjfhskf');
$i = 0;
while($i < 100) {
	if($progress->isRunning()) {
		$progress->percent($i);
		$i++;
		sleep(1);
	} else {
		break;
	}
}
$progress->close();
echo "sdfsd";
sleep(1000);
*/



class Gui {

	protected static $engine;

	public static function load($engine = 'zenity') {
		App::load($engine, 'guis');
		$engine = Inflector::camelize($engine . '_gui_engine');
		self::$engine = new $engine;
	}

	public static function showmessage($msg = '') {
		self::$engine->showmessage($msg);
	}

	public static function question($msg = '') {
		return self::$engine->question($msg);
	}

	public static function progressbar($title = '') {
		return self::$engine->progressbar($title);
		//zenity --progress --title="Sicherung" --percentage=0 --auto-close --width=400
	}

	public function radiolist($title = '', $data = array(), $default = false) {
		return self::$engine->radiolist($title, $data, $default);
	}

	public function filebrowser($filename = '') {
		return self::$engine->filebrowser($filename);
	}

	public function input($title = '', $pre = '') {
		return self::$engine->input($title, $pre);
	}
}

