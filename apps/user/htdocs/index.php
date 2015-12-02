<?php

namespace APPS\user;

\Ko_Web_Route::VGet('login', function()
{
	$render = \Ko_Apps_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
	$render = $render['key'];
	$render->oSetTemplate('passport/user/login.html')->oSend();
});

\Ko_Web_Route::VGet('reg', function()
{
	$render = \Ko_Apps_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
	$render = $render['key'];
	$render->oSetTemplate('passport/user/reg.html')->oSend();
});

\Ko_Web_Route::VGet('logo', function()
{
	$uid = \Ko_Apps_Rest::VInvoke('user', 'GET', 'loginuid/');
	if ($uid)
	{
		$render = \Ko_Apps_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
		$render = $render['key'];
		$render->oSetTemplate('passport/user/logo.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});

\Ko_Web_Route::VGet('passwd', function()
{
	$uid = \Ko_Apps_Rest::VInvoke('user', 'GET', 'loginuid/');
	if ($uid)
	{
		$render = \Ko_Apps_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
		$render = $render['key'];
		$render->oSetTemplate('passport/user/passwd.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});

\Ko_Web_Route::VGet('profile', function()
{
	$uid = \Ko_Apps_Rest::VInvoke('user', 'GET', 'loginuid/');
	if ($uid)
	{
		$render = \Ko_Apps_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
		$render = $render['key'];
		$render->oSetTemplate('passport/user/profile.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});
