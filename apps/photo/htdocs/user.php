<?php

namespace APPS\photo;

\Ko_Web_Route::VGet('index', function() {
	$uid = \Ko_Web_Request::IGet('uid');

	$photoApi = new MApi();
	$albumlist = $photoApi->getAllAlbumDigest($uid);
	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/');
	$render = $render['key'];
	$render->oSetTemplate('user.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albumlist', $albumlist)
		->oSend();
});
