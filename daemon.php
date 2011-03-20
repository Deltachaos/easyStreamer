<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
define('PATH', dirname(__FILE__));
define('DS', '/');

function __() {
	$args = func_get_args();
	$msg = $args[0];
	unset($args[0]);
	return vsprintf($msg, $args);
}

function innerHtml($node) {
    $innerHTML= '';
    $children = $node->childNodes;
    foreach ($children as $child) {
        $innerHTML .= $child->ownerDocument->saveXML( $child );
    }

    return $innerHTML;
}

require_once 'inflector.php';
require_once PATH . DS .'classes'.DS.'app.class.php';
if(App::exists('mplayer')) {
	App::$settings['player'] = 'mplayer';
} else if(App::exists('vlc')) {
	App::$settings['player'] = 'vlc';
} else if(App::exists('xine')) {
	App::$settings['player'] = 'xine';
}
if(App::exists('zenity')) {
	App::$settings['gui'] = 'zenity';
} else {
	App::$settings['gui'] = 'stdin';
}

App::parseSettings();
//var_dump(App::is('url'));

App::load('Plugin.interface', 'interfaces');
App::load('Player.interface', 'interfaces');
App::load('Gui.interface', 'interfaces');
App::load('Streamer.interface', 'interfaces');

App::load('Plugin.class', 'classes');
App::load('Gui.class', 'classes');
App::load('ProcessHandler.class', 'classes');
App::load('Process.class', 'classes');
App::load('CurlLib.class', 'classes');
App::load('Streamer.class', 'classes');
Gui::load(App::is('gui'));

if(($url = App::is('url')) === false) {
	if(App::exists('xsel')) {
		$pre = `xsel`;
	}
	if(($url = Gui::input(App::translate('Please enter the URL'), $pre)) === false) {
		die;
	}
}
$plugin = new Plugin($url);
$file = $plugin->select();
$plugin->streamer->set($plugin->stream, $file);
$plugin->streamer->download();
$plugin->streamer->fillCache($plugin->cache);

$player = $plugin->getPlayer(App::is('player'));
$player->play($file);

$asked = false;
while(ProcessHandler::isRunning('Streamer') || ProcessHandler::isRunning('Player')) {
	if(!ProcessHandler::isRunning('Player') && !$asked) {
		if(Gui::question(__('Player closed. Cancel download?'))) {
			die;
		} else {
			$asked = true;
		}
	}
	sleep(1);
}

//sleep(1000);