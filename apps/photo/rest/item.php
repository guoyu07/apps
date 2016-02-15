<?php

namespace APPS\photo;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'uid' => 'int',
			'photoid' => 'int',
		)),
		'stylelist' => array(
			'default' => array('hash', array(
				'photoid' => 'int',
				'albumid' => 'int',
				'uid' => 'int',
				'image' => 'string',
				'ctime' => 'string',
				'title' => 'string',
				'size' => array('hash', array(
					'width' => 'int',
					'height' => 'int',
				)),
			)),
		),
		'filterstylelist' => array(
			'default' => array('hash', array(
				'uid' => 'int',
				'albumid' => 'int',
			)),
		),
		'putstylelist' => array(
			'title' => 'string',
			'albumid' => 'int',
		),
	);

	public function str2key($str)
	{
		list($uid, $photoid) = explode('_', $str);
		return compact('uid', 'photoid');
	}

	public function getMulti($style, $page, $filter, $exstyle = null, $filter_style = 'default')
	{
		$photoApi = new MApi();
		switch($filter_style)
		{
			default:
				$num = $page['num'];
				$loginuid = \APPS\user\MFacade_Api::getLoginUid();

				$albuminfo = $photoApi->getAlbumInfo($filter['uid'], $filter['albumid']);
				if (empty($albuminfo) || ($albuminfo['isrecycle'] && $filter['uid'] != $loginuid)) {
					throw new \Exception('获取数据失败', 1);
				}

				$photolist = $photoApi->getPhotoListBySeq($filter['uid'], $filter['albumid'],
					$page['boundary'], $num, $next, $next_boundary, 'imageView2/2/w/240');
				return array(
					'list' => $photolist,
					'page' => array(
						'num' => $num,
						'next' => $next,
						'next_boundary' => $next_boundary,
					),
				);
		}
	}

	public function put($id, $update, $before = null, $after = null, $put_style = 'default')
	{
		$uid = \APPS\user\MFacade_Api::getLoginUid();
		if ($uid != $id['uid']) {
			throw new \Exception('修改照片失败', 1);
		}

		$photoApi = new MApi();
		switch ($put_style)
		{
			case 'title':
				$photoApi->changePhotoTitle($uid, $id['photoid'], $update);
				break;
			case 'albumid':
				$photoApi->changePhotoAlbumid($uid, $id['photoid'], $update);
				MFacade_Api::sendSysmsg($uid, $update, $id['photoid']);
				break;
		}
		return array('key' => $id);
	}

	public function delete($id, $before = null)
	{
		$uid = \APPS\user\MFacade_Api::getLoginUid();
		if ($uid != $id['uid']) {
			throw new \Exception('删除照片失败', 1);
		}

		$photoApi = new MApi;
		if (!$photoApi->deletePhoto($uid, $id['photoid'])) {
			throw new \Exception('删除照片失败', 2);
		}
		return array('key' => $id);
	}

	private function _sendSysmsg($uid, $albumid, $photoid)
	{
		if (18 <= $uid && $uid <= 21) {
			$photoApi = new MApi;
			$content = compact('uid', 'albumid', 'photoid');
			$content['photolist'] = $photoApi->getPhotoList($uid, $albumid, 0, 9, $total);
			\APPS\sysmsg\MFacade_Api::send(0, \APPS\sysmsg\MFacade_Const::PHOTO, $content, $albumid);
		}
	}
}
