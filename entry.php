<?php

define('MAIN_DOMAIN', 'zlog.cc');
define('WWW_DOMAIN', 'www.' . MAIN_DOMAIN);
define('PASSPORT_DOMAIN', 'passport.' . MAIN_DOMAIN);
define('XHPROF_DOMAIN', 'xhprof.' . MAIN_DOMAIN);
define('IMG_DOMAIN', 'img.zhangchu.cc');

define('CODE_ROOT', '/usr/share/php/');
define('COMMON_CLASS_PATH', CODE_ROOT . 'apps/include/');
define('COMMON_CONF_PATH', CODE_ROOT . 'apps/conf/');
define('COMMON_RUNDATA_PATH', CODE_ROOT . 'apps/rundata/');

define('KO_DEBUG', 1);
define('KO_TEMPDIR', COMMON_RUNDATA_PATH . 'kotmp/');
define('KO_INCLUDE_DIR', COMMON_CLASS_PATH);
define('KO_APPS_DIR', CODE_ROOT.'apps/apps/');
define('KO_APPS_NS', 'APPS');
//mysql -hrdsuurafiuurafi.mysql.rds.aliyuncs.com -udemo -pdemodemo demo
define('KO_DB_HOST', 'rdsuurafiuurafi.mysql.rds.aliyuncs.com');
define('KO_DB_USER', 'demo');
define('KO_DB_PASS', 'demodemo');
define('KO_DB_NAME', 'demo');
define('KO_MC_HOST', 'e77874bc68b911e4.m.cnbjalicm12pub001.ocs.aliyuncs.com:11211');
define('KO_SMARTY_INC', CODE_ROOT . 'Smarty-3.1.21/libs/Smarty.class.php');
define('KO_TEMPLATE_C_DIR', COMMON_RUNDATA_PATH . 'templates_c/');
define('KO_XHPROF', false);
define('KO_XHPROF_LIBDIR', CODE_ROOT . 'xhprof/xhprof_lib/');
define('KO_XHPROF_WEBBASE', 'http://' . XHPROF_DOMAIN . '/xhprof_html/');
define('KO_XHPROF_TMPDIR', COMMON_RUNDATA_PATH . 'xhprof/');

define('KO_CONFIG_SITE_INI', COMMON_CONF_PATH . 'all.ini');
define('KO_CONFIG_SITE_CACHE', COMMON_RUNDATA_PATH . 'all.php');

require_once(CODE_ROOT . 'ko/ko.class.php');

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
	//$templateroot = Ko_Web_Config::SGetValue('templateroot');
	//if (strlen($templateroot) && is_dir($templateroot)) {
	//	define('KO_TEMPLATE_DIR', $templateroot);
	//}
	//if ('passport' === $appname) {
	//	Ko_Apps_Rest::VInvoke('user', 'PUT', 'loginref/');
	//} else if ('home' === $appname) {
	//	$loginuid = Ko_Apps_Rest::VInvoke('user', 'GET', 'loginuid/');
	//	if (empty($loginuid)) {
	//		Ko_Web_Response::VSetRedirect('http://'.PASSPORT_DOMAIN.'/user/login');
	//		Ko_Web_Response::VSend();
	//		exit;
	//	}
	//}
});

Ko_Web_Event::On('ko.error', '500', function ($errno, $errstr, $errfile, $errline, $errcontext) {
	Ko_Web_Error::V500($errno, $errstr, $errfile, $errline, $errcontext);
	exit;
});

function image_AAdapter($datalist)
{
	$newdatalist = array();
	$dests_withsize = array();
	foreach ($datalist as $v) {
		if (strlen($v[0]) && isset($v[1]['withsize']) && $v[1]['withsize']) {
			$dests_withsize[] = $v[0];
		}
	}
	$sizes = Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/',
		array('filter' => $dests_withsize, 'data_style' => 'size_brief'));
	$sizes = $sizes['list'];
	foreach ($datalist as $k => $v) {
		$newdatalist[$k] = array();
		if (strlen($v[0])) {
			if (isset($sizes[$v[0]])) {
				$newdatalist[$k]['size'] = $sizes[$v[0]];
			}
			if (isset($v[1]['brief'])) {
				$newdatalist[$k]['brief'] = Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$v[0], array('data_decorate' => $v[1]['brief']));
			} else if (isset($v[1]['briefCallback'])) {
				$brief = call_user_func($v[1]['briefCallback'], $newdatalist[$k]);
				$newdatalist[$k]['brief'] = Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$v[0], array('data_decorate' => $brief));
			}
		}
	}
	return $newdatalist;
}

function user_AAdapter($datalist)
{
	$newdatalist = array();
	$uids = array();
	foreach ($datalist as $v)
	{
		$uids[] = $v[0];
	}
	$infos = Ko_Apps_Rest::VInvoke('user', 'GET', 'item/', array(
		'filter' => $uids,
	));
	$infos = $infos['list'];
	$nicknames = Ko_Apps_Rest::VInvoke('content', 'GET', 'item/', array(
		'filter' => array(
			'aid' => KContent_Const::USER_NICKNAME,
			'ids' => $uids,
		),
	));
	$nicknames = $nicknames['list'];
	foreach ($datalist as $k => $v)
	{
		$newdatalist[$k] = isset($infos[$v[0]]) ? $infos[$v[0]] : array();
		if (!empty($newdatalist[$k]))
		{
			$newdatalist[$k]['nickname'] = $nicknames[$v[0]];
			user_VFillMoreInfo($newdatalist[$k], $v[1]);
		}
	}
	return $newdatalist;
}

function user_VFillMoreInfo(&$info, $aMore)
{
	foreach ($aMore as $more)
	{
		switch($more)
		{
			case 'logo16':
				$info['logo16'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/16.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/16'));
				break;
			case 'logo32':
				$info['logo32'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/32.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/32'));
				break;
			case 'logo48':
				$info['logo48'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/48.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/48'));
				break;
			case 'logo80':
				$info['logo80'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/80.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/80'));
				break;
			case 'logo120':
				$info['logo120'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/120.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/120'));
				break;
			case 'logo200':
				$info['logo200'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/200.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/200'));
				break;
		}
	}
}

Ko_Web_Event::On('ko.dispatch', 'before', function () {
	Ko_Tool_Adapter::VOn('user_baseinfo', 'user_AAdapter');
	Ko_Tool_Adapter::VOn('image_baseinfo', 'image_AAdapter');
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
