<?php
/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 20011, Maximilian Ruta
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class DasersteDePlugin implements PluginEngine {

	const REGEX = '/^http\:\/\/mediathek.daserste.de\/(.*)$/';
	
	public $curl;

	public $stream = array();
	public $server = array();
	public $title = array();

	public function __construct($url, $curl, $plugin = null) {
		$this->curl = $curl;

		$result = $this->curl->get($url);
		$html = $result[0];
		$html = substr($html, strpos($html, 'mediaCollection.addMedia(0)') + strlen('mediaCollection.addMedia(0)'));
		$html = substr($html, 0, strpos($html, 'var pc = new PlayerConfiguration();'));
		$html = explode("\n", $html);
		$arr = array();
		$flashstreamingserver = array();
		$flashmedia = array();
		$addStream = function($dummy, $dummy2, $server, $url) use(&$flashstreamingserver, &$flashmedia) {
			$flashstreamingserver[] = $server;
			$flashmedia[] = $url;
		};
		foreach($html as $value) {
			$value = trim($value);
			if(!empty($value)) {
				$arr[] = str_replace('mediaCollection.addMediaStream', '$addStream', $value);
			}
		}
		$html = implode("\n", $arr);
		eval($html);
		$urls = array();
		$q = array(
			'Low',
			'Middle',
			'High'
		);
		$servers = array();
		foreach($flashstreamingserver as $key => $server) {
			$urls[$server.$flashmedia[$key]] = array(__('Quality') => __($q[$key]));
			$servers[$server.$flashmedia[$key]] = $server;
		}
		$this->stream = array_reverse($urls, true);
		$this->server = $servers;
		$html = $result[0];
		$html = substr($html, strpos($html, '<ul class="divBreadcrumb clearfix" style="display: none">'));
		$html = substr($html, 0, strpos($html, '</ul>') + strlen('</ul>'));
		$doc = new DOMDocument;
		$doc->loadHTML($html);
		
		$title = $doc->getElementsByTagName('li')->item(3)->textContent;
		$desc = $doc->getElementsByTagName('li')->item(4)->textContent;
		
		$html = $result[0];
		$html = substr($html, strpos($html, '<div class="boxMeta clearfix">'));
		$html = substr($html, 0, strpos($html, '</div>') + strlen('</div>'));
		$doc = new DOMDocument;
		$doc->loadHTML($html);
		$info = $doc->getElementsByTagName('span')->item(0)->textContent;
		$info = trim(substr($info, strpos($info, ' ')));
		$date = strtotime($info);

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
	}

}


