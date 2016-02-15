<?php

namespace APPS\storage;

$api = new MApi();
if ($api->bCheckCallback('/storage/'))
{
	$data = \Ko_Web_Request::APost();
	if (!$api->bAsyncImage2Storage($data['key'], $data))
	{
		$data = array('errno' => 2, 'error' => '保存信息失败');
	}
	else
	{
		switch ($data['type'])
		{
			case 'blog':
			case 'logo':
				$data = array(
					'key' => $data['key'],
					'after' => MFacade_Api::getUrl($data['key'], $data['decorate']),
				);
				break;
			case 'photo':
				$data = \APPS\photo\MFacade_Api::addPhoto($data['albumid'], $data['uid'], $data['key'], $data['name'], $data['decorate']);
				break;
		}
		$data = array('errno' => 0, 'error' => '', 'data' => $data);
	}
}
else
{
	$data = array('errno' => 1, 'error' => '验证失败');
}

$render = new \Ko_View_Render_JSON();
$render->oSetData($data)->oSend();
