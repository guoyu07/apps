<?php

namespace APPS\photo;

\Ko_Web_Route::VGet('index', function() {
	$uid = \Ko_Web_Request::IGet('uid');

	$photoApi = new MApi();
	$albumlist = $photoApi->getAllAlbumDigest($uid);
	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$loginuid = \APPS\user\MFacade_Api::getLoginUid();
	$token = \APPS\storage\MFacade_Api::getUploadImageToken(array('type' => 'photo',
		'uid' => $loginuid, 'albumid' => 0, 'decorate' => 'imageView2/2/w/150/h/150'));

	$render = new \APPS\render\MFacade_default();
	$render->oSetTemplate('user.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albumlist', $albumlist)
		->oSetData('token', $token)
		->oSend();
});
