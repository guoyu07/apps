<?php

namespace APPS\content;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'aid' => 'int',
			'id' => 'int',
		)),
		'stylelist' => array(
			'default' => 'string',
			'html' => 'string',
		),
		'filterstylelist' => array(
			'default' => array('hash', array(
				'aid' => 'int',
				'ids' => array('list', 'int'),
				'maxlength' => 'int',
				'ext' => 'string',
			)),
			'html' => array('hash', array(
				'aid' => 'int',
				'ids' => array('list', 'int'),
				'maxlength' => 'int',
			)),
		),
		'putstylelist' => array(
			'default' => 'string',
		),
	);

	public function str2key($str)
	{
		list($aid, $id) = explode('_', $str);
		return compact('aid', 'id');
	}

	public function get($id, $style = null)
	{
		$api = new MApi();
		switch ($style['style'])
		{
			case 'html':
				return $api->sGetHtml($id['aid'], $id['id'], $style['decorate']['maxlength']);
		}
		return $api->sGetText($id['aid'], $id['id'], $style['decorate']['maxlength'], $style['decorate']['ext']);
	}

	public function getMulti($style, $page, $filter, $ex_style = null, $filter_style = 'default')
	{
		$api = new MApi();
		switch ($filter_style)
		{
			case 'html':
				$list = $api->aGetHtml($filter['aid'], $filter['ids'], $filter['maxlength']);
				return array('list' => $list);
		}
		$list = $api->aGetText($filter['aid'], $filter['ids'], $filter['maxlength'], $filter['ext']);
		return array('list' => $list);
	}

	public function put($id, $update, $before_style = null, $after_style = null, $put_style = 'default')
	{
		$api = new MApi();
		$api->bSet($id['aid'], $id['id'], $update);
		return array();
	}
}
