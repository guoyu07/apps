<?php

namespace APPS\storage;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => 'string',
		'stylelist' => array(
			'default' => 'string',
			'content' => 'string',
			'size' => array('hash', array(
				'width' => 'int',
				'height' => 'int',
			)),
			'exif' => 'any',
			'size_brief' => array('hash', array(
				'brief' => 'string',
				'size' => array('hash', array(
					'width' => 'int',
					'height' => 'int',
				)),
			)),
		),
		'filterstylelist' => array(
			'default' => array('list', 'string'),
		),
		'poststylelist' => array(
			'default' => array('hash', array(
				'notonlyimage' => 'bool',
			)),
			'upload' => array('hash', array(
				'file' => 'any',
				'notonlyimage' => 'bool',
			)),
			'content' => array('hash', array(
				'content' => 'string',
				'notonlyimage' => 'bool',
			)),
			'weburl' => array('hash', array(
				'url' => 'string',
				'notonlyimage' => 'bool',
			)),
		),
	);

	public function get($id, $style = null)
	{
		$api = new MApi();
		switch($style['style'])
		{
			case 'content':
				return $api->sRead($id);
			case 'exif':
				return $api->aGetImageExif($id);
			case 'size':
				return $api->aGetImageSize($id);
		}
		return $api->sGetUrl($id, $style['decorate']);
	}

	public function getMulti($style, $page, $filter, $ex_style = null, $filter_style = 'default')
	{
		$api = new MApi();
		switch ($filter_style)
		{
			case 'default':
				switch ($style['style'])
				{
					case 'size_brief':
						$sizes = $api->aGetImagesSize($filter);
						foreach ($sizes as $k => &$v)
						{
							if (strlen($style['decorate']))
							{
								$v['brief'] = $api->sGetUrl($k, $style['decorate']);
							}
							$v['size'] = $v;
						}
						unset($v);
						return array('list' => $sizes);
				}
				break;
		}
		return array('list' => array());
	}

	public function post($update, $after_style = null, $post_style = 'default')
	{
		$api = new MApi();
		switch ($post_style)
		{
			case 'upload':
				if (!$api->bUpload2Storage($update['file'], $sDest, !$update['notonlyimage']))
				{
					throw new \Exception('文件上传失败', 1);
				}
				return array('key' => $sDest);
			case 'content':
				if (!$api->bContent2Storage($update['content'], $sDest, !$update['notonlyimage']))
				{
					throw new \Exception('文件上传失败', 1);
				}
				return array('key' => $sDest);
			case 'weburl':
				if (!$api->bWebUrl2Storage($update['url'], $sDest, !$update['notonlyimage']))
				{
					throw new \Exception('文件上传失败', 1);
				}
				return array('key' => $sDest);
			default:
				$file = \Ko_Web_Request::AFile('file');
				if (!$api->bUpload2Storage($file, $sDest, !$update['notonlyimage']))
				{
					throw new \Exception('文件上传失败', 1);
				}
				$data = array('key' => $sDest);
				if (is_array($after_style))
				{
					switch($after_style['style'])
					{
						default:
							$data['after'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$data['key'],
								array('data_decorate' => $after_style['decorate']));
							break;
					}
				}
				return $data;
		}
	}
}
