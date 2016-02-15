<?php

namespace APPS\storage;

class MFacade_Api
{
	public static function upload2storage($file, $onlyimage)
	{
		$api = new MApi();
		if (!$api->bUpload2Storage($file, $dest, $onlyimage))
		{
			return false;
		}
		return $dest;
	}

	public static function weburl2storage($url, $onlyimage)
	{
		$api = new MApi();
		if (!$api->bWebUrl2Storage($url, $dest, $onlyimage))
		{
			return false;
		}
		return $dest;
	}

	public static function content2storage($content, $onlyimage)
	{
		$api = new MApi();
		if (!$api->bContent2Storage($content, $dest, $onlyimage))
		{
			return false;
		}
		return $dest;
	}

	public static function getUrl($id, $decorate)
	{
		$api = new MApi();
		return $api->sGetUrl($id, $decorate);
	}

	public static function getExif($id)
	{
		$api = new MApi();
		return $api->aGetImageExif($id);
	}

	public static function getContent($id)
	{
		$api = new MApi();
		return $api->sRead($id);
	}

	public static function getSizeAndBrief($ids, $decorate)
	{
		$api = new MApi();
		$sizes = $api->aGetImagesSize($ids);
		foreach ($sizes as $k => &$v)
		{
			$tmp = array();
			if (strlen($decorate))
			{
				$tmp['brief'] = $api->sGetUrl($k, $decorate);
			}
			$tmp['size'] = $v;
			$v = $tmp;
		}
		unset($v);
		return $sizes;
	}

	public static function getUploadImageToken($callbackInfo)
	{
		$api = new MApi();
		return $api->sGetUploadImageToken('http://callback.'.MAIN_DOMAIN.'/storage/', $callbackInfo);
	}

	public static function getUploadVideoToken($callbackInfo)
	{
		$api = new MApi();
		return $api->sGetUploadVideoToken('http://callback.'.MAIN_DOMAIN.'/storage/video',
			$callbackInfo,
			'http://callback.'.MAIN_DOMAIN.'/storage/video/notify');
	}

	public static function getCover($data)
	{
		$offset = 0;
		$key = '';
		$api = new MApi();
		while (1) {
			$url = self::_sGetImageUrl($data, $offset);
			if ('' === $url) {
				break;
			}
			list($dest, ) = $api->aParseUrl($url);
			if ('' !== $dest) {
				$key = $dest;
				break;
			}
		}
		return $key;
	}

	private static function _sGetImageUrl($content, &$offset)
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
