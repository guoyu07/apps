<?php

namespace APPS\user;

\Ko_Web_Route::VGet('index', function()
{
	$render = new \APPS\render\MFacade_passport();
	$render->oSetTemplate('login.html')->oSend();
});
