<?php

namespace APPS\user\oauth2;

class MRest_login
{
	public static $s_aConf = array(
		'unique' => 'string',
		'stylelist' => array(
			'default' => array(
				'tokeninfo' => 'any',
				'username' => 'string',
				'userinfo' => 'any',
			),
		),
	);

	public function get($id, $style = null)
	{
		$api = new MApi;
		$tokeninfo = $api->aGetTokenInfo($id);
		if (!$api->bGetUserinfoByTokeninfo($id, $tokeninfo, $username, $userinfo)) {
			throw new \Exception('µÇÂ¼Ê§°Ü', 1);
		}
		return compact('tokeninfo', 'username', 'userinfo');
	}
}
