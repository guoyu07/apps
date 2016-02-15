<?php

namespace APPS\photo;

\Ko_Web_Route::VGet('index', function () {
	$loginuid = \APPS\user\MFacade_Api::getLoginUid();

	$uid = \Ko_Web_Request::IGet('uid');
	$photoid = \Ko_Web_Request::IGet('photoid');

	$photoApi = new MApi();
	$photoinfo = $photoApi->getPhotoInfo($uid, $photoid);
	if (empty($photoinfo)) {
		\Ko_Web_Response::VSetRedirect('/');
		\Ko_Web_Response::VSend();
		exit;
	}
	$photoinfo['image_src'] = \APPS\storage\MFacade_Api::getUrl($photoinfo['image'], '');
	$photoinfo['image_small'] = \APPS\storage\MFacade_Api::getUrl($photoinfo['image'], 'imageView2/1/w/60');
	$photoinfo['image_exif'] = \APPS\storage\MFacade_Api::getExif($photoinfo['image']);
	$agentinfo = \APPS\user\MFacade_agentApi::get();
	if ($agentinfo['screen']['height'] < 1000) {
		$photoinfo['image'] = \APPS\storage\MFacade_Api::getUrl($photoinfo['image'], 'imageView2/2/w/600/h/600');
		$photoinfo['imagesize'] = 600;
	} else {
		$photoinfo['image'] = \APPS\storage\MFacade_Api::getUrl($photoinfo['image'], 'imageView2/2/w/800/h/800');
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
		$curinfo['image'] = \APPS\storage\MFacade_Api::getUrl($curinfo['image'], 'imageView2/1/w/60');
		array_unshift($prevlist, $curinfo);
		if (count($prevlist) >= 4) {
			break;
		}
	}
	$curinfo = $photoinfo;
	while (!empty($curinfo = $photoApi->getNextPhotoInfo($curinfo))) {
		$curinfo['image'] = \APPS\storage\MFacade_Api::getUrl($curinfo['image'], 'imageView2/1/w/60');
		array_push($nextlist, $curinfo);
		if (count($nextlist) >= 15 - count($prevlist)) {
			break;
		}
	}
	if (!empty($prevlist) && count($prevlist) + count($nextlist) < 15) {
		$curinfo = $prevlist[0];
		while (!empty($curinfo = $photoApi->getPrevPhotoInfo($curinfo))) {
			$curinfo['image'] = \APPS\storage\MFacade_Api::getUrl($curinfo['image'], 'imageView2/1/w/60');
			array_unshift($prevlist, $curinfo);
			if (count($prevlist) >= 15 - count($nextlist)) {
				break;
			}
		}
	}

	$render = new \APPS\render\MFacade_default();
	$render->oSetTemplate('item.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('albuminfo', $albuminfo)
		->oSetData('photoinfo', $photoinfo)
		->oSetData('prevlist', $prevlist)
		->oSetData('nextlist', $nextlist)
		->oSend();
});
