<?php

namespace APPS\user;

\Ko_Web_Route::VGet('index', function()
{
	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/', array('post_style' => 'passport'));
	$render = $render['key'];
	$render->oSetTemplate('reg.html')->oSend();
});
