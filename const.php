<?php

define('MAIN_DOMAIN', 'zlog.cc');
define('WWW_DOMAIN', 'www.' . MAIN_DOMAIN);
define('PASSPORT_DOMAIN', 'passport.' . MAIN_DOMAIN);
define('XHPROF_DOMAIN', 'xhprof.' . MAIN_DOMAIN);
define('IMG_DOMAIN', 'img.yuxiaoyan.cn');

define('CODE_ROOT', '/usr/share/php/');
define('COMMON_CONF_PATH', CODE_ROOT . 'apps/conf/');
define('COMMON_RUNDATA_PATH', CODE_ROOT . 'apps/rundata/');

define('KO_DEBUG', 1);
define('KO_RUNTIME_DIR', COMMON_RUNDATA_PATH);
define('KO_TEMPDIR', COMMON_RUNDATA_PATH . 'kotmp/');
define('KO_APPS_DIR', CODE_ROOT . 'apps/apps/');
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

function user_AAdapter($datalist)
{
	$newdatalist = array();
	$uids = array();
	foreach ($datalist as $v) {
		$uids[] = $v[0];
	}
	$infos = \APPS\user\MFacade_Api::getUsersInfo($uids);

	$contentApi = new \APPS\content\MFacade_Api();
	$nicknames = $contentApi->aGetText(\APPS\content\MFacade_Const::USER_NICKNAME, $uids);

	foreach ($datalist as $k => $v) {
		$newdatalist[$k] = isset($infos[$v[0]]) ? $infos[$v[0]] : array();
		if (!empty($newdatalist[$k])) {
			$newdatalist[$k]['nickname'] = $nicknames[$v[0]];
			user_VFillMoreInfo($newdatalist[$k], $v[1]);
		}
	}
	return $newdatalist;
}

function user_VFillMoreInfo(&$info, $aMore)
{
	foreach ($aMore as $more) {
		switch ($more) {
			case 'logo16':
				$info['logo16'] = ('' === $info['logo'])
					? 'http://' . IMG_DOMAIN . '/logo/16.png'
					: \APPS\storage\MFacade_Api::getUrl($info['logo'], 'imageView2/1/w/16');
				break;
			case 'logo32':
				$info['logo32'] = ('' === $info['logo'])
					? 'http://' . IMG_DOMAIN . '/logo/32.png'
					: \APPS\storage\MFacade_Api::getUrl($info['logo'], 'imageView2/1/w/32');
				break;
			case 'logo48':
				$info['logo48'] = ('' === $info['logo'])
					? 'http://' . IMG_DOMAIN . '/logo/48.png'
					: \APPS\storage\MFacade_Api::getUrl($info['logo'], 'imageView2/1/w/48');
				break;
			case 'logo80':
				$info['logo80'] = ('' === $info['logo'])
					? 'http://' . IMG_DOMAIN . '/logo/80.png'
					: \APPS\storage\MFacade_Api::getUrl($info['logo'], 'imageView2/1/w/80');
				break;
			case 'logo120':
				$info['logo120'] = ('' === $info['logo'])
					? 'http://' . IMG_DOMAIN . '/logo/120.png'
					: \APPS\storage\MFacade_Api::getUrl($info['logo'], 'imageView2/1/w/120');
				break;
			case 'logo200':
				$info['logo200'] = ('' === $info['logo'])
					? 'http://' . IMG_DOMAIN . '/logo/200.png'
					: \APPS\storage\MFacade_Api::getUrl($info['logo'], 'imageView2/1/w/200');
				break;
		}
	}
}

Ko_Tool_Adapter::VOn('user_baseinfo', 'user_AAdapter');
