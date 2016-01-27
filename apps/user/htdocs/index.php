<?php

namespace APPS\user;

\Ko_Web_Route::VGet('login', function()
{
	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
	$render = $render['key'];
	$render->oSetTemplate('login.html')->oSend();
});

\Ko_Web_Route::VGet('reg', function()
{
	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
	$render = $render['key'];
	$render->oSetTemplate('reg.html')->oSend();
});

\Ko_Web_Route::VGet('logo', function()
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

\Ko_Web_Route::VGet('passwd', function()
{
	$uid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');
	if ($uid)
	{
		$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
		$render = $render['key'];
		$render->oSetTemplate('passwd.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});

\Ko_Web_Route::VGet('profile', function()
{
	$uid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');
	if ($uid)
	{
		$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
		$render = $render['key'];
		$render->oSetTemplate('profile.html')->oSend();
	}
	else
	{
		\Ko_Web_Response::VSetRedirect('login');
		\Ko_Web_Response::VSend();
	}
});
