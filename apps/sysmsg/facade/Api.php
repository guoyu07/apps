<?php

namespace APPS\sysmsg;

class MFacade_Api
{
	public static function send($uid, $msgtype, array $content, $mergeid = null)
	{
		$api = new MApi();
		return $api->iSend($uid, $msgtype, $content, $mergeid);
	}

	public static function getIndexList($boundary, $num, &$next, &$next_boundary)
	{
		$api = new MApi();
		return $api->getIndexList($boundary, $num, $next, $next_boundary);
	}
}
