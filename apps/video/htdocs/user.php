<?php

namespace APPS\video;

\Ko_Web_Route::VGet('index', function() {
	$uid = \Ko_Web_Request::IGet('uid');

	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$loginuid = \APPS\user\MFacade_Api::getLoginUid();
	$token = \APPS\storage\MFacade_Api::getUploadVideoToken(array('uid' => $loginuid));

	$api = new MApi();
	$videolist = $api->getUserList($uid);

	$render = new \APPS\render\MFacade_default();
	$render->oSetTemplate('user.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('token', $token)
		->oSetData('videolist', $videolist)
		->oSend();
});
