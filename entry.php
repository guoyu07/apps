<?php

include_once('const.php');

Ko_Web_Event::On('ko.config', 'after', function () {
	$appname = Ko_Web_Config::SGetAppName();
	if ('' === $appname) {
		Ko_Web_Response::VSetRedirect('http://' . WWW_DOMAIN);
		Ko_Web_Response::VSend();
		exit;
	}
	if (!Ko_Tool_Safe::BCheckMethod(array('*.' . MAIN_DOMAIN))) {
		Ko_Web_Response::VSetHttpCode(403);
		Ko_Web_Response::VSend();
		exit;
	}
	$templateroot = Ko_Web_Config::SGetValue('templateroot');
	if (strlen($templateroot) && is_dir($templateroot)) {
		define('KO_TEMPLATE_DIR', $templateroot);
	}
	$host = Ko_Web_Request::SHttpHost();
	if (PASSPORT_DOMAIN === $host) {
		Ko_App_Rest::VInvoke('user', 'PUT', 'loginref/');
	} else if (WWW_DOMAIN === $host) {
		$loginuid = Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');
		if (empty($loginuid)) {
			Ko_Web_Response::VSetRedirect('http://'.PASSPORT_DOMAIN.'/user/login');
			Ko_Web_Response::VSend();
			exit;
		}
	}
});

Ko_Web_Event::On('ko.error', '500', function ($errno, $errstr, $errfile, $errline, $errcontext) {
	Ko_Web_Error::V500($errno, $errstr, $errfile, $errline, $errcontext);
	exit;
});

Ko_Web_Event::On('ko.dispatch', 'before', function () {
	$host = Ko_Web_Request::SHttpHost();
	if ('zc.'.MAIN_DOMAIN === $host) {
		$_GET['uid'] = 20;
	}
});

Ko_Web_Event::On('ko.dispatch', '404', function () {
	Ko_Web_Route::V404();
	exit;
});

require_once(KO_DIR . 'web/Bootstrap.php');
