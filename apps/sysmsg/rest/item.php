<?php

namespace APPS\sysmsg;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'uid' => 'int',
			'msgid' => 'int',
		)),
		'stylelist' => array(
			'default' => array('hash', array(
				'uid' => 'int',
				'msgid' => 'int',
				'msgtype' => 'int',
				'stime' => 'int',
				'content' => 'any',
				'ctime' => 'string',
			)),
		),
		'filterstylelist' => array(
			'default' => 'any',
		),
		'poststylelist' => array(
			'default' => array('hash', array(
				'uid' => 'int',
				'msgtype' => 'int',
				'content' => 'any',
				'mergeid' => 'int',
			)),
		),
	);

	public function getMulti($style, $page, $filter, $exstyle = null, $filter_style = 'default')
	{
		$num = $page['num'];
		$sysmsgApi = new MApi;
		$msglist = $sysmsgApi->getIndexList($page['boundary'], $num, $next, $next_boundary);
		$page = compact('num', 'next', 'next_boundary');
		return array(
			'list' => $msglist,
			'page' => $page,
		);
	}

	public function post($update, $after_style = null, $post_style = 'default')
	{
		$api = new MApi();
		$msgid = $api->iSend($update['uid'], $update['msgtype'], $update['content'], $update['mergeid'] ?: null);
		return array('key' => array(
			'uid' => $update['uid'],
			'msgid' => $msgid,
		));
	}
}
