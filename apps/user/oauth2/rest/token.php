<?php

namespace APPS\user\oauth2;

class MRest_token
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'src' => 'string',
			'uid' => 'int',
			'token' => 'string',
		)),
		'poststylelist' => array(
			'default' => array(
				'src' => 'string',
				'uid' => 'int',
				'tokeninfo' => 'any',
			),
		),
	);

	public function post($update, $after_style = null, $post_style = 'default')
	{
		$api = new MApi;
		$api->bSaveUserToken($update['src'], $update['uid'], $update['tokeninfo']);
		return array();
	}
}
