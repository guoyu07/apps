<?php

namespace APPS\storage;

$api = new MApi();
if ($api->bCheckCallback('/storage/'))
{
	$data = \Ko_Web_Request::APost();
	if (!$api->bAsync2Storage($data['key'], $data))
	{
		$data = array('errno' => 2, 'error' => '保存信息失败');
	}
	$data = \Ko_App_Rest::VInvoke('photo', 'POST', 'item/',
		array('post_style' => 'async', 'update' => $data,
			'after_style' => 'default', 'after_decorate' => 'imageView2/2/w/150/h/150'));
	$data = array('errno' => 0, 'error' => '', 'data' => $data);
}
else
{
	$data = array('errno' => 1, 'error' => '验证失败');
}

$render = new \Ko_View_Render_JSON();
$render->oSetData($data)->oSend();
