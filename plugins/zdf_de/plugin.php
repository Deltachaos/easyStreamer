<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class ZdfDePlugin implements PluginEngine {

	const REGEX = "/^http:\/\/www.zdf.de\/ZDFmediathek\/beitrag\/video\/([0-9]+)\/(.*)$/";

	public function __construct($url, $curl, $plugin = null) {
		$this->curl = $curl;

		$result = $this->curl->get($url);
		$html = $result[0];
		$html = substr($html, strpos($html, '<p class="player">Windows Media Player</p>') + strlen('<p class="player">Windows Media Player</p>'));
		$html = substr($html, 0, strpos($html, '</ul>'));
		$html .= '</ul>';
		$doc = new DOMDocument;
		$doc->loadHTML($html);

		$links = array();
		$links[] = $doc->getElementsByTagName('li')->item(0)->getElementsByTagName('a')->item(0)->getAttribute('href');
		$links[] = $doc->getElementsByTagName('li')->item(1)->getElementsByTagName('a')->item(0)->getAttribute('href');
		$q = array(
			'Low',
			'High'
		);
		foreach($links as $key => $url) {
			$urls[$url] = array(__('Quality') => __($q[$key]));
		}
		$this->stream = array_reverse($urls, true);
		//print_r($urls);

		$html = $result[0];
		$html = substr($html, strpos($html, '<h1 class="beitragHeadline">'));
		$html = substr($html, 0, strpos($html, '</h1>'));
		$html .= '</h1>';
		$doc = new DOMDocument;
		$doc->loadHTML($html);
		
		$title = $doc->getElementsByTagName('h1')->item(0)->textContent;

		//<p class="datum">

		$html = $result[0];
		$html = substr($html, strpos($html, '<p class="datum">'));
		$html = substr($html, 0, strpos($html, '</p>'));
		$html .= '</p>';
		$doc = new DOMDocument;
		$doc->loadHTML($html);

		$tmp = $doc->getElementsByTagName('p')->item(0)->textContent;

		$date = strtotime(trim(substr($tmp, strrpos($tmp, ',') + 1)));
		$desc = trim(substr($tmp, 0, strrpos($tmp, ',')));

		$this->title = array(
			$title . '.wmv',
			$title . ' - ' . $desc . '.wmv',
			$desc . '.wmv',
			$title . ', ' . date('d.m.Y', $date) . '.wmv',
			$title . ' - ' . $desc . ', ' . date('d.m.Y', $date) . '.wmv',
			$desc . ', ' . date('d.m.Y', $date) . '.wmv',
			date('d.m.Y', $date) . '.wmv'
		);
		$titles = array();
		foreach($this->title as $key => $title) {
			$titles[$title] = array(__('Title') => utf8_encode($title));
		}
		$this->title = $titles;
/*
		$title = trim($doc->getElementsByTagName('h3')->item(0)->textContent);
		$desc = trim($doc->getElementsByTagName('p')->item(0)->textContent);
		$info = trim($doc->getElementsByTagName('p')->item(1)->textContent);
		$info = trim(substr($info, 0, strpos($info, ' ')));

		$html = $result[0];
		$html = substr($html, strpos($html, 'player.flashstreamingserver[\'1\'] = "'));
		$html = substr($html, 0, strpos($html, "\n\t\t\t\n", strpos($html, "\n\t\t\t\n") + 1));
		$flashstreamingserver = array();
		$flashmedia = array();
		$html = str_replace(array("player.avaible_url['flashmedia']", "player.flashstreamingserver"), array('$flashmedia', '$flashstreamingserver'), $html);
		eval($html);
		$flashstreamingserver = array_values($flashstreamingserver);
		$flashmedia = array_values($flashmedia);
		$urls = array();
		$date = strtotime($info);
		$servers = array();
		foreach($flashstreamingserver as $key => $server) {
			$urls[$server.$flashmedia[$key]] = array(__('Quality') => __($q[$key]));
			$servers[$server.$flashmedia[$key]] = $server;
		}
		$this->stream = array_reverse($urls, true);
		$this->server = $servers;
		$this->title = array(
			$title . '.mp4',
			$title . ' - ' . $desc . '.mp4',
			$desc . '.mp4',
			$title . ', ' . date('d.m.Y', $date) . '.mp4',
			$title . ' - ' . $desc . ', ' . date('d.m.Y', $date) . '.mp4',
			$desc . ', ' . date('d.m.Y', $date) . '.mp4',
			date('d.m.Y', $date) . '.mp4'
		);
		$titles = array();
		foreach($this->title as $key => $title) {
			$titles[$title] = array(__('Title') => $title);
		}
		$this->title = $titles;
  */
	}

}