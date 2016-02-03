<?php

namespace APPS\photo;

\Ko_Web_Route::VGet('index', function() {
	$uid = \Ko_Web_Request::IGet('uid');

	$photoApi = new MApi();
	$albumlist = $photoApi->getAllAlbumDigest($uid);
	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$loginuid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');
	$token = \Ko_App_Rest::VInvoke('storage', 'POST', 'token/',
		array('update' => array('uid' => $loginuid, 'albumid' => 0)));
	$token = $token['key'];

	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/');
	$render = $render['key'];
	$render->oSetTemplate('user.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albumlist', $albumlist)
		->oSetData('token', $token)
		->oSend();
});
