<?php

namespace APPS\user\oauth2;

class MFacade_Api
{
	public static function saveUserToken($src, $uid, $tokeninfo)
	{
		$api = new MApi;
		return $api->bSaveUserToken($src, $uid, $tokeninfo);
	}

	public static function getTokenInfo($src)
	{
		$api = new MApi;
		$tokeninfo = $api->aGetTokenInfo($src);
		if (!$api->bGetUserinfoByTokeninfo($src, $tokeninfo, $username, $userinfo)) {
			return false;
		}
		return compact('tokeninfo', 'username', 'userinfo');
	}
}
