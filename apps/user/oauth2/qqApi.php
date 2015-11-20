<?php

namespace APPS\user\oauth2;

class MqqApi extends \Ko_Busi_Api
{
	public function bGetUserinfoByTokeninfo($aSrcConf, $aTokeninfo, &$sUsername, &$aUserinfo)
	{
		$uri = 'https://graph.qq.com/oauth2.0/me?access_token='.urlencode($aTokeninfo['access_token']);
		$response = file_get_contents($uri);
		$response = trim(str_replace(array('callback(', ');'), '', $response));
		$meinfo = json_decode($response, true);
		if (!isset($meinfo['openid']))
		{
			return false;
		}
		$uri = 'https://graph.qq.com/user/get_user_info?access_token='.urlencode($aTokeninfo['access_token']).'&oauth_consumer_key='.urlencode($meinfo['client_id']).'&openid='.urlencode($meinfo['openid']);
		$response = file_get_contents($uri);
		$aUserinfo = json_decode($response, true);
		if (0 != $aUserinfo['ret'])
		{
			return false;
		}
		$sUsername = $meinfo['openid'];
		$logo = $aUserinfo['figureurl_qq_2'];
		if (empty($logo))
		{
			$logo = $aUserinfo['figureurl_qq_1'];
		}
		$aUserinfo = array('nickname' => $aUserinfo['nickname'], 'logo' => $logo);
		return true;
	}
	
	public static function AGetAccessToken($sUri)
	{
		$response = file_get_contents($sUri);
		parse_str($response, $arr);
		return json_encode($arr);
	}
}
