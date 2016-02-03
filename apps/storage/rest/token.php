<?php

namespace APPS\storage;

class MRest_token
{
	public static $s_aConf = array(
		'unique' => 'string',
		'poststylelist' => array(
			'default' => array('hash', array(
				'uid' => 'int',
				'albumid' => 'int',
			)),
		),
	);

	public function post($update, $after_style = null, $post_style = 'default')
	{
		$api = new MApi();
		return array('key' => $api->sGetUploadToken('http://callback.'.MAIN_DOMAIN.'/storage/', $update));
	}
}
