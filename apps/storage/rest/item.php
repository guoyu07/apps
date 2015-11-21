<?php

namespace APPS\storage;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => 'string',
		'stylelist' => array(
			'default' => 'string',
			'size' => array('hash', array(
				'brief' => 'string',
				'size' => array('hash', array(
					'width' => 'int',
					'height' => 'int',
				)),
			)),
		),
		'filterstylelist' => array(
			'default' => array('list', 'string'),
		),
	);

	public function get($id, $style = null)
	{
		$api = new MApi();
		return $api->sGetUrl($id, $style['decorate']);
	}

	public function getMulti($style, $page, $filter, $ex_style = null, $filter_style = 'default')
	{
		$api = new MApi();
		switch ($filter_style)
		{
			case 'default':
				switch ($style['style'])
				{
					case 'size':
						$sizes = $api->aGetImagesSize($filter);
						foreach ($sizes as $k => &$v)
						{
							if (strlen($style['decorate']))
							{
								$v['brief'] = $api->sGetUrl($k, $style['decorate']);
							}
							$v['size'] = $v;
						}
						unset($v);
						return array('list' => $sizes);
				}
				break;
		}
		return array('list' => array());
	}
}
