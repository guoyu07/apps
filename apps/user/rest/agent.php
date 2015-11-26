<?php

namespace APPS\user;

class MRest_agent
{
	public static $s_aConf = array(
		'stylelist' => array(
			'default' => array('hash', array(
				'screen' => array('hash', array(
					'width' => 'int',
					'height' => 'int',
				)),
			)),
		),
		'putstylelist' => array(
			'default' => array('hash', array(
				'screen' => array('hash', array(
					'width' => 'int',
					'height' => 'int',
				)),
			)),
		),
	);

	public function get($id, $style = null)
	{
		return MagentApi::get();
	}

	public function put($id, $update, $before = null, $after = null, $put_style)
	{
		MagentApi::set($update);
	}
}
