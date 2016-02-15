<?php

namespace APPS\photo;

class MFacade_Api
{
	public static function getPhotoInfos($photoids)
	{
		$api = new MApi();
		return $api->getPhotoInfos($photoids);
	}

	public static function getAlbumInfos($albumids)
	{
		$api = new MApi();
		return $api->getAlbumInfos($albumids);
	}

	public static function addPhoto($albumid, $uid, $image, $title, $decorate)
	{
		$photoApi = new MApi;
		$photoid = $photoApi->addPhoto($albumid, $uid, $image, $title);
		self::sendSysmsg($uid, $albumid, $photoid);
		$data = array('key' => compact('uid', 'photoid'));
		$data['after'] = $photoApi->getPhotoInfo($uid, $photoid);
		$data['after']['image'] = \APPS\storage\MFacade_Api::getUrl($image, $decorate);
		return $data;
	}

	public static function sendSysmsg($uid, $albumid, $photoid)
	{
		if (18 <= $uid && $uid <= 21) {
			$photoApi = new MApi;
			$content = compact('uid', 'albumid', 'photoid');
			$content['photolist'] = $photoApi->getPhotoList($uid, $albumid, 0, 9, $total);
			\APPS\sysmsg\MFacade_Api::send(0, \APPS\sysmsg\MFacade_Const::PHOTO, $content, $albumid);
		}
	}
}
