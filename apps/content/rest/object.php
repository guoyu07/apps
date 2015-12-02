<?php

namespace APPS\content;

class MRest_object
{
	public static $s_aConf = array(
		'unique' => 'any',
		'poststylelist' => array(
			'default' => 'any',
		),
	);

	public function post($update, $after = null, $post_style = 'default')
	{
		return array('key' => new MApi());
	}
}
