<?php

namespace APPS\user;

class MFacade_loginrefApi
{
	const COOKIE_NAME = 'ref';

	public static function VInit()
	{
		$referer = \Ko_Web_Request::SHttpReferer();
		if ('' != $referer)
		{
			$rinfo = parse_url($referer);
			if (PASSPORT_DOMAIN !== $rinfo['host']
				&& (MAIN_DOMAIN === $rinfo['host']
					|| '.'.MAIN_DOMAIN === substr($rinfo['host'], -1 - strlen(MAIN_DOMAIN))))
			{
				\Ko_Web_Response::VSetCookie(self::COOKIE_NAME, $referer);
			}
		}
	}

	public static function SGet()
	{
		$ref = \Ko_Web_Request::SCookie(self::COOKIE_NAME);
		if ('' == $ref)
		{
			return 'http://'.MAIN_DOMAIN;
		}
		return $ref;
	}
}
