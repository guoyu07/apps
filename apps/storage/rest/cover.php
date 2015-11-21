<?php

namespace APPS\storage;

class MRest_cover
{
	public static $s_aConf = array(
		'unique' => 'string',
		'poststylelist' => array(
			'default' => 'string',
		),
	);

	public function post($update, $after_style = null, $post_style = 'default')
	{
		$offset = 0;
		$key = '';
		$api = new MApi();
		while (1) {
			$url = $this->_sGetImageUrl($update, $offset);
			if ('' === $url) {
				break;
			}
			list($dest, $brief) = $api->aParseUrl($url);
			if ('' !== $dest) {
				$key = $dest;
				break;
			}
		}
		return array('key' => $key);
	}

	private function _sGetImageUrl($content, &$offset)
	{
		$spos = strpos($content, ' src=', $offset);
		if (false === $spos) {
			return '';
		}
		$quotes = substr($content, $spos + 5, 1);
		if ('"' !== $quotes && "'" !== $quotes) {
			return '';
		}
		$offset = strpos($content, $quotes, $spos + 6);
		if (false === $offset) {
			return '';
		}
		return substr($content, $spos + 6, $offset - $spos - 6);
	}
}
