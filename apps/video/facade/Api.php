<?php

namespace APPS\video;

class MFacade_Api
{
	public static function add($uid, $video, $persistentid)
	{
		$api = new MApi();
		return $api->add($uid, $video, $persistentid);
	}

	public static function updatePinfo($persistentid, $p_code, $p_info)
	{
		$api = new MApi();
		return $api->updatePinfo($persistentid, $p_code, $p_info);
	}
}
