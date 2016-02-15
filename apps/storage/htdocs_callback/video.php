<?php

namespace APPS\storage;

\Ko_Web_Route::VPost('index', function(){
	$api = new MApi();
	if ($api->bCheckCallback('/storage/video'))
	{
		$data = \Ko_Web_Request::APost();
		$data['avinfo'] = json_decode($data['avinfo'], true);
		if (!$api->bAsynsFile2Storage($data['key'], $data))
		{
			$data = array('errno' => 2, 'error' => '保存信息失败');
		}
		else
		{
			$api->vSetAvinfo($data['key'], $data['avinfo']);
			$videoid = \APPS\video\MFacade_Api::add($data['uid'], $data['key'], $data['persistentId']);
			if (empty($videoid))
			{
				$data = array('errno' => 3, 'error' => '保存用户信息失败');
			}
			else
			{
				$data['videoid'] = $videoid;
				$data = array('errno' => 0, 'error' => '', 'data' => $data);
			}
		}
	}
	else
	{
		$data = array('errno' => 1, 'error' => '验证失败');
	}

	$render = new \Ko_View_Render_JSON();
	$render->oSetData($data)->oSend();
});

\Ko_Web_Route::VPost('notify', function(){
	$input = file_get_contents('php://input');
	$input = json_decode($input, true);

	\APPS\video\MFacade_Api::updatePinfo($input['id'], $input['code'], $input);
});
