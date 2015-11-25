<?php

namespace APPS\photo;

\Ko_Web_Route::VGet('user', function() {
	$uid = \Ko_Web_Request::IGet('uid');

	$photoApi = new MApi();
	$albumlist = $photoApi->getAllAlbumDigest($uid);
	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$render = new \KRender_default;
	$render->oSetTemplate('default/photo/user.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albumlist', $albumlist)
		->oSend();
});

\Ko_Web_Route::VGet('album', function () {
	static $num = 20;

	$loginApi = new \KUser_loginApi();
	$loginuid = $loginApi->iGetLoginUid();

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

	$render = new \KRender_default;
	if ($loginuid == $uid) {
		$allalbumlist = $photoApi->getAllAlbumList($uid);
		$render->oSetData('allalbumlist', $allalbumlist);
	}
	$render->oSetTemplate('default/photo/album.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albuminfo', $albuminfo)
		->oSetData('photolist', $photolist)
		->oSetData('page', array(
			'num' => $num,
			'next' => $next,
			'next_boundary' => $next_boundary,
		))
		->oSend();
});

\Ko_Web_Route::VGet('item', function () {
	$loginApi = new \KUser_loginApi();
	$loginuid = $loginApi->iGetLoginUid();

	$uid = \Ko_Web_Request::IGet('uid');
	$photoid = \Ko_Web_Request::IGet('photoid');

	$photoApi = new MApi();
	$photoinfo = $photoApi->getPhotoInfo($uid, $photoid);
	if (empty($photoinfo)) {
		\Ko_Web_Response::VSetRedirect('/');
		\Ko_Web_Response::VSend();
		exit;
	}
	$photoinfo['image_src'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$photoinfo['image'], array('data_decorate' => ''));
	$photoinfo['image_small'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$photoinfo['image'], array('data_decorate' => 'imageView2/1/w/60'));
	$photoinfo['image_exif'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$photoinfo['image'], array('data_style' => 'exif'));
	$agentinfo = \KUser_agentApi::get();
	if ($agentinfo['screen']['height'] < 1000) {
		$photoinfo['image'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$photoinfo['image'], array('data_decorate' => 'imageView2/2/w/600/h/600'));
		$photoinfo['imagesize'] = 600;
	} else {
		$photoinfo['image'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$photoinfo['image'], array('data_decorate' => 'imageView2/2/w/800/h/800'));
		$photoinfo['imagesize'] = 800;
	}
	$albuminfo = $photoApi->getAlbumInfo($uid, $photoinfo['albumid']);
	if ($albuminfo['isrecycle'] && $uid != $loginuid) {
		\Ko_Web_Response::VSetRedirect('/');
		\Ko_Web_Response::VSend();
		exit;
	}
	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$prevlist = $nextlist = array();
	$curinfo = $photoinfo;
	while (!empty($curinfo = $photoApi->getPrevPhotoInfo($curinfo))) {
		$curinfo['image'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$curinfo['image'], array('data_decorate' => 'imageView2/1/w/60'));
		array_unshift($prevlist, $curinfo);
		if (count($prevlist) >= 4) {
			break;
		}
	}
	$curinfo = $photoinfo;
	while (!empty($curinfo = $photoApi->getNextPhotoInfo($curinfo))) {
		$curinfo['image'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$curinfo['image'], array('data_decorate' => 'imageView2/1/w/60'));
		array_push($nextlist, $curinfo);
		if (count($nextlist) >= 15 - count($prevlist)) {
			break;
		}
	}
	if (!empty($prevlist) && count($prevlist) + count($nextlist) < 15) {
		$curinfo = $prevlist[0];
		while (!empty($curinfo = $photoApi->getPrevPhotoInfo($curinfo))) {
			$curinfo['image'] = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$curinfo['image'], array('data_decorate' => 'imageView2/1/w/60'));
			array_unshift($prevlist, $curinfo);
			if (count($prevlist) >= 15 - count($nextlist)) {
				break;
			}
		}
	}

	$render = new \KRender_default;
	$render->oSetTemplate('default/photo/item.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albuminfo', $albuminfo)
		->oSetData('photoinfo', $photoinfo)
		->oSetData('prevlist', $prevlist)
		->oSetData('nextlist', $nextlist)
		->oSend();
});
