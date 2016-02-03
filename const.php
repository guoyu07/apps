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
define('KO_RUNTIME_DIR', COMMON_RUNDATA_PATH);
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
define('KO_XHPROF', false);
define('KO_XHPROF_LIBDIR', CODE_ROOT . 'xhprof/xhprof_lib/');
define('KO_XHPROF_WEBBASE', 'http://' . XHPROF_DOMAIN . '/xhprof_html/');
define('KO_XHPROF_TMPDIR', COMMON_RUNDATA_PATH . 'xhprof/');

define('KO_CONFIG_SITE_INI', COMMON_CONF_PATH . 'site.ini');

require_once(CODE_ROOT . 'ko/ko.class.php');

function image_AAdapter($datalist)
{
	$newdatalist = array();
	$dests_withsize = array();
	foreach ($datalist as $v) {
		if (strlen($v[0]) && isset($v[1]['withsize']) && $v[1]['withsize']) {
			$dests_withsize[] = $v[0];
		}
	}
	$sizes = Ko_App_Rest::VInvoke('storage', 'GET', 'item/',
		array('filter' => $dests_withsize, 'data_style' => 'size_brief'));
	$sizes = $sizes['list'];
	foreach ($datalist as $k => $v) {
		$newdatalist[$k] = array();
		if (strlen($v[0])) {
			if (isset($sizes[$v[0]])) {
				$newdatalist[$k]['size'] = $sizes[$v[0]];
			}
			if (isset($v[1]['brief'])) {
				$newdatalist[$k]['brief'] = Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$v[0], array('data_decorate' => $v[1]['brief']));
			} else if (isset($v[1]['briefCallback'])) {
				$brief = call_user_func($v[1]['briefCallback'], $newdatalist[$k]);
				$newdatalist[$k]['brief'] = Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$v[0], array('data_decorate' => $brief));
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
	$infos = Ko_App_Rest::VInvoke('user', 'GET', 'item/', array(
		'filter' => $uids,
	));
	$infos = $infos['list'];
	$nicknames = Ko_App_Rest::VInvoke('content', 'GET', 'item/', array(
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
					: Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/16'));
				break;
			case 'logo32':
				$info['logo32'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/32.png'
					: Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/32'));
				break;
			case 'logo48':
				$info['logo48'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/48.png'
					: Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/48'));
				break;
			case 'logo80':
				$info['logo80'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/80.png'
					: Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/80'));
				break;
			case 'logo120':
				$info['logo120'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/120.png'
					: Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/120'));
				break;
			case 'logo200':
				$info['logo200'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/200.png'
					: Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/200'));
				break;
		}
	}
}

Ko_Tool_Adapter::VOn('user_baseinfo', 'user_AAdapter');
Ko_Tool_Adapter::VOn('image_baseinfo', 'image_AAdapter');
