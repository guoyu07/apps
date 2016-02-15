<?php

namespace APPS\blog;

class MFacade_Api
{
	public static function getInfos($blogids)
	{
		$api = new MApi();
		return $api->aGetBlogInfos($blogids);
	}
}
