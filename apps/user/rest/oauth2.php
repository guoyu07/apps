<?php

namespace APPS\user;

class MRest_oauth2
{
	public static $s_aConf = array(
		'unique' => 'string',
		'stylelist' => array(
			'default' => 'int',
		),
	);

	public function get($id, $style = null)
	{
		$api = new MloginApi();
		return $api->iOauth2Login($id);
	}
}
