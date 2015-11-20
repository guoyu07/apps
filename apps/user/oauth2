<?php

class KUser_Oauth2_baiduApi extends Ko_Busi_Api
{
	public function bGetUserinfoByTokeninfo($aSrcConf, $aTokeninfo, &$sUsername, &$aUserinfo)
	{
		$uri = 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo?access_token='.urlencode($aTokeninfo['access_token']);
		$response = file_get_contents($uri);
		$aUserinfo = json_decode($response, true);
		if (!$aUserinfo['userid'])
		{
			return false;
		}
		$sUsername = $aUserinfo['userid'];
		$aUserinfo = array('nickname' => $aUserinfo['username'], 'logo' => 'http://himg.bdimg.com/sys/portrait/item/'.$aUserinfo['portrait'].'.jpg');
		return true;
	}
}
