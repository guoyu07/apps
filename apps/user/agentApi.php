<?php

namespace APPS\user;

class MagentApi
{
	const COOKIE_NAME = 'agent';

	public static function set($update)
	{
		\Ko_Web_Response::VSetCookie(self::COOKIE_NAME, json_encode($update), 0, '/', '.'.MAIN_DOMAIN);
	}

	public static function get()
	{
		$str = \Ko_Web_Request::SCookie(self::COOKIE_NAME);
		return json_decode($str, true);
	}
}
