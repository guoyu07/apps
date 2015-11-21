<?php

namespace APPS\storage;

class MRest_dataurl
{
	public static $s_aConf = array(
		'unique' => 'string',
		'poststylelist' => array(
			'default' => 'string',
		),
	);

	public function post($update, $after_style = null, $post_style = 'default')
	{
		$api = new MApi();
		if ($api->bContent2Storage($update, $sDest))
		{
			$key = $api->sGetUrl($sDest, 'imageView2/2/w/600/h/600');
			return array('key' => $key);
		}
		throw new \Exception('转换失败', 1);
	}
}