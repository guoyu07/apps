<?php

namespace APPS\user\oauth2;

\Ko_Web_Route::VGet('qq', function () {
	oauth2login('qq');
});

\Ko_Web_Route::VGet('weibo', function () {
	oauth2login('weibo');
});

\Ko_Web_Route::VGet('baidu', function () {
	oauth2login('baidu');
});

function oauth2login($src)
{
	$uid = \Ko_Apps_Rest::VInvoke('user', 'GET', 'oauth2/'.$src);
	\Ko_Apps_Rest::VInvoke('user', 'PUT', 'loginuid/', array('update' => $uid));
	$loginref = \Ko_Apps_Rest::VInvoke('user', 'GET', 'loginref/');
	\Ko_Web_Response::VSetRedirect($loginref);
	\Ko_Web_Response::VSend();
}
