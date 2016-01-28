<?php

namespace APPS\user;

\Ko_Web_Route::VGet('index', function()
{
	$uid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');
	if ($uid)
	{
		$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
		$render = $render['key'];
		$render->oSetTemplate('logo.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});
