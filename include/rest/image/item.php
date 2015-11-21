<?php

class KRest_Image_item
{
	public static $s_aConf = array(
		'unique' => 'string',
		'stylelist' => array(
			'default' => 'string',
		),
		'poststylelist' => array(
			'default' => 'any',
		),
	);

	public function post($update, $after = null)
	{
		$file = Ko_Web_Request::AFile('file');
		$data = Ko_Apps_Rest::VInvoke('storage', 'POST', 'item/', array(
			'post_style' => 'upload',
			'update' => array(
				'file' => $file,
			),
		), $error);
		if ($error)
		{
			throw new Exception('文件上传失败', 1);
		}
		if (is_array($after))
		{
			switch($after['style'])
			{
				default:
					$data['after'] = Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$data['key'], array('data_decorate' => $after['decorate']));
					break;
			}
		}
		return $data;
	}
}
