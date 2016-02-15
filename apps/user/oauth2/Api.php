<?php

namespace APPS\user\oauth2;

class MApi extends \Ko_Mode_OAuth2Client
{
	protected $_aConf = array(
		'usertoken' => 'usertoken',
		'lasttoken' => 'lasttoken',
		'srclist' => array(
			'qq' => array(
				'client_id' => '101198149',
				'client_secret' => '5f024b109b679f4a9245a009c7321776',
				'authorize_uri' => 'https://graph.qq.com/oauth2.0/authorize',
				'token_uri' => 'https://graph.qq.com/oauth2.0/token',
//				'redirect_uri' => 'http://'.PASSPORT_DOMAIN.'/oauth2/qq',
				'request_method' => 'GET',
				),
			'weibo' => array(
				'client_id' => '3126701786',
				'client_secret' => 'c0b557270ded8adcff156a9628587449',
				'authorize_uri' => 'https://api.weibo.com/oauth2/authorize',
				'token_uri' => 'https://api.weibo.com/oauth2/access_token',
//				'redirect_uri' => 'http://'.PASSPORT_DOMAIN.'/oauth2/weibo',
				'request_method' => 'POST',
				),
			'baidu' => array(
				'client_id' => 'qKjm8H2UaHjCpbfazpwimSFq',
				'client_secret' => 'IbB7GXNfB3mG5eDD2kRfIAbUgYtCgvM1',
				'authorize_uri' => 'https://openapi.baidu.com/oauth/2.0/authorize',
				'token_uri' => 'https://openapi.baidu.com/oauth/2.0/token',
//				'redirect_uri' => 'http://'.PASSPORT_DOMAIN.'/oauth2/baidu',
				'request_method' => 'GET',
				),
			),
		);

	public static function oauth2login($src)
	{
		$uid = \APPS\user\MFacade_Api::oauth2login($src);
		\APPS\user\MFacade_Api::setLoginUid($uid);
		$loginref = \APPS\user\MFacade_loginrefApi::SGet();
		\Ko_Web_Response::VSetRedirect($loginref);
		\Ko_Web_Response::VSend();
	}

	public function __construct()
	{
		foreach ($this->_aConf['srclist'] as $k => &$v)
		{
			$v['redirect_uri'] = 'http://'.PASSPORT_DOMAIN.'/oauth2/'.$k;
		}
		unset($v);
	}
	
	public function aGetTokenInfo($sSrc)
	{
		switch($sSrc)
		{
		case 'qq':
			$fnGetToken = array('APPS\\user\\oauth2\\MqqApi', 'AGetAccessToken');
			break;
		case 'weibo':
			$fnGetToken = array('APPS\\user\\oauth2\\MweiboApi', 'AGetAccessToken');
			break;
		case 'baidu':
			$fnGetToken = 'file_get_contents';
			break;
		}
		return $this->vMain($sSrc, $fnGetToken);
	}

	public function bGetUserinfoByTokeninfo($sSrc, $aTokeninfo, &$sUsername, &$aUserinfo)
	{
		$srcApi = $sSrc.'Api';
		return $this->$srcApi->bGetUserinfoByTokeninfo($this->_aConf['srclist'][$sSrc], $aTokeninfo, $sUsername, $aUserinfo);
	}
}
