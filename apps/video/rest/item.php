<?php

namespace APPS\video;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'uid' => 'int',
			'videoid' => 'int',
		)),
		'putstylelist' => array(
			'title' => 'string',
			'recover' => 'any',
		),
	);

	public function str2key($str)
	{
		list($uid, $videoid) = explode('_', $str);
		return compact('uid', 'videoid');
	}

	public function put($id, $update, $before = null, $after = null, $put_style = 'default')
	{
		$uid = \APPS\user\MFacade_Api::getLoginUid();
		if ($uid != $id['uid']) {
			throw new \Exception('修改视频失败', 1);
		}

		$videoApi = new MApi();
		switch ($put_style)
		{
			case 'title':
				$videoApi->changeTitle($uid, $id['videoid'], $update);
				break;
			case 'recover':
				$videoApi->recover($uid, $id['videoid']);
				break;
		}
		return array('key' => $id);
	}

	public function delete($id, $before = null)
	{
		$uid = \APPS\user\MFacade_Api::getLoginUid();
		if ($uid != $id['uid']) {
			throw new \Exception('删除视频失败', 1);
		}

		$videoApi = new MApi;
		if (!$videoApi->del($uid, $id['videoid'])) {
			throw new \Exception('删除视频失败', 2);
		}
		return array('key' => $id);
	}
}
