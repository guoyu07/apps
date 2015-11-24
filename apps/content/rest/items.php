<?php

namespace APPS\content;

class MRest_items
{
	public static $s_aConf = array(
		'unique' => 'int',
		'stylelist' => array(
			'default' => array('list', 'string'),
			'html' => array('list', 'string'),
		),
		'filterstylelist' => array(
			'default' => array('list', 'any'),
			'html' => array('list', 'any'),
		),
	);

	public function getMulti($style, $page, $filter, $ex_style = null, $filter_style = 'default')
	{
		$api = new MApi();
		switch ($filter_style)
		{
			case 'html':
				$list = $api->aGetHtmlEx($filter);
				return array('list' => $list);
		}
		$list = $api->aGetTextEx($filter);
		return array('list' => $list);
	}
}
