<?php

namespace APPS\user;

class MRest_loginref
{
	public static $s_aConf = array(
		'stylelist' => array(
			'default' => 'string',
		),
		'putstylelist' => array(
			'default' => 'string',
		),
	);

	public function get($id, $style = null)
	{
		return MloginrefApi::SGet();
	}

	public function put($id, $update, $before = null, $after = null, $put_style)
	{
		MloginrefApi::VInit();
	}
}
