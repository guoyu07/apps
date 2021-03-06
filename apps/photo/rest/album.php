<?php

namespace APPS\photo;

class MRest_album
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'uid' => 'int',
			'albumid' => 'int',
		)),
		'stylelist' => array(
			'default' => array('hash', array(
				'albumid' => 'int',
				'uid' => 'int',
				'ctime' => 'string',
				'mtime' => 'string',
				'pcount' => 'int',
				'title' => 'string',
			)),
		),
		'poststylelist' => array(
			'default' => array('hash', array(
				'title' => 'string',
				'desc' => 'string',
			)),
		),
		'putstylelist' => array(
			'title' => 'string',
			'desc' => 'string',
		),
	);

	public function str2key($str)
	{
		list($uid, $albumid) = explode('_', $str);
		return compact('uid', 'albumid');
	}

	public function post($update, $after = null)
	{
		if (0 == strlen($update['title'])) {
			throw new \Exception('请输入相册标题', 1);
		}

		$uid = \APPS\user\MFacade_Api::getLoginUid();

		$photoApi = new MApi();
		$albumid = $photoApi->addAlbum($uid, $update['title'], $update['desc']);
		if (!$albumid) {
			throw new \Exception('添加相册失败', 1);
		}
		return array('key' => $id);
	}

	public function put($id, $update, $before = null, $after = null, $put_style = 'default')
	{
		$uid = \APPS\user\MFacade_Api::getLoginUid();

		if ($uid != $id['uid']) {
			throw new \Exception('修改相册失败', 1);
		}

		$photoApi = new MApi();
		switch ($put_style)
		{
			case 'title':
				$photoApi->changeAlbumTitle($uid, $id['albumid'], $update);
				break;
			case 'desc':
				$photoApi->changeAlbumDesc($uid, $id['albumid'], $update);
				break;
		}
		return array('key' => $id);
	}

	public function delete($id, $before = null)
	{
		$uid = \APPS\user\MFacade_Api::getLoginUid();
		if ($uid != $id['uid']) {
			throw new \Exception('删除相册失败', 1);
		}

		$photoApi = new MApi();
		if (!$photoApi->deleteAlbum($id['uid'], $id['albumid'])) {
			throw new \Exception('删除相册失败', 2);
		}
		return array('key' => $id);
	}
}
