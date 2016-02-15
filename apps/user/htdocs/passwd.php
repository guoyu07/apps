<?php

namespace APPS\user;

\Ko_Web_Route::VGet('index', function()
{
	$uid = MFacade_Api::getLoginUid();
	if ($uid)
	{
		$render = new \APPS\render\MFacade_passport();
		$render->oSetTemplate('passwd.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});
