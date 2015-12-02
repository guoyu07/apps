<?php

namespace APPS\render;

class MRest_object
{
	public static $s_aConf = array(
		'unique' => 'any',
		'poststylelist' => array(
			'default' => 'any',
			'passport' => 'any',
		),
	);

	public function post($update, $after = null, $post_style = 'default')
	{
		switch ($post_style)
		{
			case 'passport':
				return array('key' => new Mpassport());
		}
		return array('key' => new Mdefault());
	}
}