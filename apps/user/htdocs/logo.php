<?php

namespace APPS\user;

\Ko_Web_Route::VGet('index', function()
{
	$uid = MFacade_Api::getLoginUid();
	if ($uid)
	{
		$token = \APPS\storage\MFacade_Api::getUploadImageToken(array('type' => 'logo', 'decorate' => 'imageView2/3/w/400/h/400'));
		$render = new \APPS\render\MFacade_passport();
		$render->oSetTemplate('logo.html')->oSetData('token', $token)->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});
