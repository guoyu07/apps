<?php

namespace APPS\photo;

\Ko_Web_Route::VGet('index', function () {
	static $num = 20;

	$loginuid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');

	$uid = \Ko_Web_Request::IGet('uid');
	$albumid = \Ko_Web_Request::IGet('albumid');

	$photoApi = new MApi();
	$albuminfo = $photoApi->getAlbumInfo($uid, $albumid);
	if (empty($albuminfo) || ($albuminfo['isrecycle'] && $uid != $loginuid)) {
		\Ko_Web_Response::VSetRedirect('/');
		\Ko_Web_Response::VSend();
		exit;
	}

	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));
	$photolist = $photoApi->getPhotoListBySeq($uid, $albumid, '0_0_0', $num, $next, $next_boundary, 'imageView2/2/w/240');

	$token = \Ko_App_Rest::VInvoke('storage', 'POST', 'token/',
		array('update' => array('uid' => $loginuid, 'albumid' => $albumid)));
	$token = $token['key'];

	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/');
	$render = $render['key'];
	if ($loginuid == $uid) {
		$allalbumlist = $photoApi->getAllAlbumList($uid);
		$render->oSetData('allalbumlist', $allalbumlist);
	}
	$render->oSetTemplate('album.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albuminfo', $albuminfo)
		->oSetData('photolist', $photolist)
		->oSetData('page', array(
			'num' => $num,
			'next' => $next,
			'next_boundary' => $next_boundary,
		))
		->oSetData('token', $token)
		->oSend();
});
