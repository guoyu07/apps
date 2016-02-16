<?php

namespace APPS\video;

class MFacade_Api
{
	public static function add($uid, $video, $persistentid, $title)
	{
		$api = new MApi();
		return $api->add($uid, $video, $persistentid, $title);
	}

	public static function updatePinfo($persistentid, $p_code, $p_info)
	{
		$api = new MApi();
		return $api->updatePinfo($persistentid, $p_code, $p_info);
	}

	public static function getInfos($list)
	{
		$api = new MApi();
		return $api->getInfos($list);
	}
}
