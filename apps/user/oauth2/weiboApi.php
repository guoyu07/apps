<?php

namespace APPS\user\oauth2;

class MweiboApi extends \Ko_Busi_Api
{
	public function bGetUserinfoByTokeninfo($aSrcConf, $aTokeninfo, &$sUsername, &$aUserinfo)
	{
		$uri = 'https://api.weibo.com/2/users/show.json?access_token='.urlencode($aTokeninfo['access_token']).'&uid='.urlencode($aTokeninfo['uid']);
		$response = file_get_contents($uri);
		$aUserinfo = json_decode($response, true);
		if (!$aUserinfo['id'])
		{
			return false;
		}
		$sUsername = $aUserinfo['id'];
		$aUserinfo = array('nickname' => $aUserinfo['screen_name'], 'logo' => $aUserinfo['avatar_large']);
		return true;
	}
	
	public static function AGetAccessToken($sUri)
	{
		$ch = curl_init('https://api.weibo.com/oauth2/access_token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sUri);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}
