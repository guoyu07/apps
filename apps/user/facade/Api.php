<?php

namespace APPS\user;

class MFacade_Api
{
	public static function getLoginUid()
	{
		$api = new MloginApi();
		return $api->iGetLoginUid();
	}

	public static function setLoginUid($uid)
	{
		$api = new MloginApi();
		$api->vSetLoginUid($uid);
	}

	public static function getUsersInfo($uids)
	{
		$api = new MbaseinfoApi();
		return $api->aGetListByKeys($uids);
	}

	public static function oauth2login($src)
	{
		$api = new MloginApi();
		return $api->iOauth2Login($src);
	}
}
