<?php

namespace APPS\test;

class MRest_demo
{
	public static $s_aConf = array(
		'unique' => 'int',
		'stylelist' => array(
			'default' => array('hash', array(
				'id' => 'int',
				'name' => 'string',
			)),
		),
	);

	public function get($id, $style = null)
	{
		return array(
			'id' => $id,
			'name' => 'zhang',
		);
	}
}
