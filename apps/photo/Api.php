<?php

namespace APPS\photo;

class MApi extends \Ko_Busi_Api
{
	public function getPhotoInfos($list)
	{
		return $this->photoDao->aGetDetails($list);
	}

	public function getAlbumInfos($list)
	{
		$albumids = \Ko_Tool_Utils::AObjs2ids($list, 'albumid');
		$infos = $this->albumDao->aGetDetails($list);
		$aText = \Ko_App_Rest::VInvoke('content', 'GET', 'items/', array(
			'filter' => array(
				\KContent_Const::PHOTO_ALBUM_TITLE => $albumids,
				\KContent_Const::PHOTO_ALBUM_DESC => $albumids,
			),
		));
		$aText = $aText['list'];
		foreach ($infos as &$v) {
			$v['title'] = $aText[\KContent_Const::PHOTO_ALBUM_TITLE][$v['albumid']];
			$v['desc'] = $aText[\KContent_Const::PHOTO_ALBUM_DESC][$v['albumid']];
		}
		unset($v);
		return $infos;
	}

	public function getPhotoInfo($uid, $photoid)
	{
		$photokey = compact('uid', 'photoid');
		$info = $this->photoDao->aGet($photokey);
		if (!empty($info)) {
			$info['title'] = \Ko_App_Rest::VInvoke('content', 'GET', 'item/'.\KContent_Const::PHOTO_TITLE.'_'.$photoid);
		}
		return $info;
	}

	public function getPrevPhotoInfo($photoinfo)
	{
		$photoid = $photoinfo['prev'];
		if (!$photoid) {
			return array();
		}
		$previnfo = $this->getPhotoInfo($photoinfo['uid'], $photoid);
		if (!empty($previnfo)) {
			if ($previnfo['pos'] + 1 != $photoinfo['pos']) {
				$update = array('pos' => $previnfo['pos'] + 1);
				$this->photoDao->iUpdate($photoinfo, $update);
			}
		}
		return $previnfo;
	}

	public function getNextPhotoInfo($photoinfo)
	{
		$photoid = $photoinfo['next'];
		if (!$photoid) {
			return array();
		}
		$nextinfo = $this->getPhotoInfo($photoinfo['uid'], $photoid);
		if (!empty($nextinfo)) {
			if ($nextinfo['pos'] != $photoinfo['pos'] + 1) {
				$update = array('pos' => $photoinfo['pos'] + 1);
				$this->photoDao->iUpdate($nextinfo, $update);
			}
		}
		return $nextinfo;
	}

	public function getAlbumInfo($uid, $albumid)
	{
		$albumkey = compact('uid', 'albumid');
		$info = $this->albumDao->aGet($albumkey);
		if (!empty($info)) {
			$aText = \Ko_App_Rest::VInvoke('content', 'GET', 'items/', array(
				'filter' => array(
					\KContent_Const::PHOTO_ALBUM_TITLE => array($albumid),
					\KContent_Const::PHOTO_ALBUM_DESC => array($albumid),
				),
			));
			$aText = $aText['list'];
			$info['title'] = $aText[\KContent_Const::PHOTO_ALBUM_TITLE][$albumid];
			$info['desc'] = $aText[\KContent_Const::PHOTO_ALBUM_DESC][$albumid];
			$info['isrecycle'] = $info['albumid'] == $this->_getRecycleAlbumid($uid);
		}
		return $info;
	}

	public function getPhotoListBySeq($uid, $albumid, $boundary, $num, &$next, &$next_boundary, $decorate)
	{
		$next = 0;
		if (!$uid) {
			return array();
		}
		$albumkey = compact('uid', 'albumid');
		$album = $this->albumDao->aGet($albumkey);
		if (empty($album)) {
			return array();
		}
		list($boundary_photoid, $boundary_sort, $boundary_pos) = explode('_', $boundary);
		$option = new \Ko_Tool_SQL();
		if ($boundary_photoid) {
			$option->oAnd('sort < ? OR (sort = ? AND photoid < ?)', $boundary_sort, $boundary_sort, $boundary_photoid);
		}
		$limit = $num + 1;
		$option->oAnd('albumid = ?', $albumid)->oLimit($limit)->oOrderBy('sort desc, photoid desc');
		$photolist = $this->photoDao->aGetList($option);
		if ($count = count($photolist)) {
			if ($count > $num) {
				$next = array_pop($photolist);
				$count--;
				$next = $next['photoid'];
			}
		}
		$photoids = \Ko_Tool_Utils::AObjs2ids($photolist, 'photoid');
		$aText = \Ko_App_Rest::VInvoke('content', 'GET', 'item/', array(
			'filter' => array(
				'aid' => \KContent_Const::PHOTO_TITLE,
				'ids' => $photoids,
			),
		));
		$aText = $aText['list'];
		$images = \Ko_Tool_Utils::AObjs2ids($photolist, 'image');
		$sizes = \Ko_App_Rest::VInvoke('storage', 'GET', 'item/',
			array('filter' => $images, 'data_style' => 'size_brief', 'data_decorate' => $decorate));
		foreach ($photolist as $k => &$v) {
			$v['size'] = $sizes['list'][$v['image']]['size'];
			$v['image'] = $sizes['list'][$v['image']]['brief'];
			$v['title'] = $aText[$v['photoid']];

			$update = array();
			if ($boundary_pos + $k + 1 != $v['pos']) {
				$update['pos'] = $v['pos'] = $boundary_pos + $k + 1;
			}
			if ($k != 0) {
				if ($photolist[$k - 1]['photoid'] != $v['prev']) {
					$update['prev'] = $v['prev'] = $photolist[$k - 1]['photoid'];
				}
			} else {
				if ($boundary_photoid != $v['prev']) {
					$update['prev'] = $v['prev'] = $boundary_photoid;
				}
			}
			if ($k != $count - 1) {
				if ($photolist[$k + 1]['photoid'] != $v['next']) {
					$update['next'] = $v['next'] = $photolist[$k + 1]['photoid'];
				}
			} else {
				if ($next != $v['next']) {
					$update['next'] = $v['next'] = $next;
				}
				$next_boundary = $v['photoid'].'_'.$v['sort'].'_'.$v['pos'];
			}
			if (!empty($update)) {
				$this->photoDao->iUpdate($v, $update);
			}
		}
		unset($v);
		if (!$next && $album['pcount'] != $boundary_pos + $count) {
			$this->albumDao->iUpdate($albumkey, array('pcount' => $boundary_pos + $count));
		}
		return $photolist;
	}

	public function getPhotoList($uid, $albumid, $start, $num, &$total)
	{
		$total = 0;
		if (!$uid) {
			return array();
		}
		$albumkey = compact('uid', 'albumid');
		$album = $this->albumDao->aGet($albumkey);
		if (empty($album)) {
			return array();
		}
		$option = new \Ko_Tool_SQL();
		$offset = ($start > 0) ? $start - 1 : $start;
		$addnum = ($start > 0) ? 2 : 1;
		$limit = $num + $addnum;
		$option->oAnd('albumid = ?', $albumid)->oOffset($offset)->oLimit($limit)->oCalcFoundRows(true)->oOrderBy('sort desc, photoid desc');
		$photolist = $this->photoDao->aGetList($option);
		if ($count = count($photolist)) {
			if ($start > 0) {
				$prev = array_shift($photolist);
				$count--;
				$prev = $prev['photoid'];
			} else {
				$prev = 0;
			}
			if ($count > $num) {
				$next = array_pop($photolist);
				$count--;
				$next = $next['photoid'];
			} else {
				$next = 0;
			}
		}
		$total = $option->iGetFoundRows();
		if ($total != $album['pcount']) {
			$this->albumDao->iUpdate($albumkey, array('pcount' => $total));
		}
		foreach ($photolist as $k => &$v) {
			$update = array();
			if ($start + $k + 1 != $v['pos']) {
				$update['pos'] = $v['pos'] = $start + $k + 1;
			}
			if ($k != 0) {
				if ($photolist[$k - 1]['photoid'] != $v['prev']) {
					$update['prev'] = $v['prev'] = $photolist[$k - 1]['photoid'];
				}
			} else {
				if ($prev != $v['prev']) {
					$update['prev'] = $v['prev'] = $prev;
				}
			}
			if ($k != $count - 1) {
				if ($photolist[$k + 1]['photoid'] != $v['next']) {
					$update['next'] = $v['next'] = $photolist[$k + 1]['photoid'];
				}
			} else {
				if ($next != $v['next']) {
					$update['next'] = $v['next'] = $next;
				}
			}
			if (!empty($update)) {
				$this->photoDao->iUpdate($v, $update);
			}
		}
		unset($v);
		return $photolist;
	}

	public function getAllAlbumList($uid)
	{
		if (!$uid) {
			return array();
		}
		$option = new \Ko_Tool_SQL();
		$option->oWhere('uid = ?', $uid)->oOrderBy('sort desc');
		$albumlist = $this->albumDao->aGetList($option);
		$albumids = \Ko_Tool_Utils::AObjs2ids($albumlist, 'albumid');
		$aText = \Ko_App_Rest::VInvoke('content', 'GET', 'items/', array(
			'filter' => array(
				\KContent_Const::PHOTO_ALBUM_TITLE => $albumids,
				\KContent_Const::PHOTO_ALBUM_DESC => $albumids,
			),
		));
		$aText = $aText['list'];
		$recycleid = $this->_getRecycleAlbumid($uid);
		foreach ($albumlist as &$v) {
			$v['title'] = $aText[\KContent_Const::PHOTO_ALBUM_TITLE][$v['albumid']];
			$v['desc'] = $aText[\KContent_Const::PHOTO_ALBUM_DESC][$v['albumid']];
			$v['isrecycle'] = $v['albumid'] == $recycleid;
		}
		unset($v);
		return $albumlist;
	}

	public function getAllAlbumDigest($uid)
	{
		if (!$uid) {
			return array();
		}
		$option = new \Ko_Tool_SQL();
		$option->oWhere('uid = ?', $uid)->oOrderBy('sort desc');
		$albumlist = $this->albumDao->aGetList($option);
		$albumids = \Ko_Tool_Utils::AObjs2ids($albumlist, 'albumid');
		$digest = $this->_getDigest($albumids);
		$allphotoids = array();
		foreach ($digest as &$v) {
			$v = array_slice($v, 0, 45);
			$allphotoids = array_merge($allphotoids, $v);
		}
		unset($v);
		foreach ($allphotoids as &$v) {
			$v = array(
				'uid' => $uid,
				'photoid' => $v,
			);
		}
		unset($v);
		$photoinfos = $this->photoDao->aGetDetails($allphotoids);
		$aText = \Ko_App_Rest::VInvoke('content', 'GET', 'items/', array(
			'filter' => array(
				\KContent_Const::PHOTO_ALBUM_TITLE => $albumids,
				\KContent_Const::PHOTO_ALBUM_DESC => $albumids,
			),
		));
		$aText = $aText['list'];
		$recycleid = $this->_getRecycleAlbumid($uid);
		foreach ($albumlist as &$v) {
			$v['title'] = $aText[\KContent_Const::PHOTO_ALBUM_TITLE][$v['albumid']];
			$v['desc'] = $aText[\KContent_Const::PHOTO_ALBUM_DESC][$v['albumid']];
			$v['isrecycle'] = $v['albumid'] == $recycleid;
			$v['digest'] = $digest[$v['albumid']];
			foreach ($v['digest'] as &$vv) {
				$vv = $photoinfos[$vv];
				$vv['image'] = \Ko_App_Rest::VInvoke('storage', 'GET', 'item/'.$vv['image'], array('data_decorate' => 'imageView2/1/w/60'));
			}
			unset($vv);
		}
		unset($v);
		return $albumlist;
	}

	public function changePhotoTitle($uid, $photoid, $title)
	{
		if (!$uid) {
			return false;
		}
		$photokey = compact('uid', 'photoid');
		$photo = $this->photoDao->aGet($photokey);
		if (empty($photo)) {
			return false;
		}
		\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::PHOTO_TITLE.'_'.$photoid, array(
			'update' => $title,
		));
		return true;
	}

	public function changePhotoAlbumid($uid, $photoid, $albumid)
	{
		if (!$uid) {
			return false;
		}
		$photokey = compact('uid', 'photoid');
		$photo = $this->photoDao->aGet($photokey);
		if (empty($photo)) {
			return false;
		}
		$albumkey = compact('uid', 'albumid');
		$album = $this->albumDao->aGet($albumkey);
		if (empty($album)) {
			return false;
		}
		$this->photoDao->iUpdate($photokey, array('albumid' => $albumid, 'sort' => time()));
		$this->albumDao->iUpdate($albumkey, array(), array('pcount' => 1));
		$option = new \Ko_Tool_SQL();
		$this->albumDao->iUpdate($photo, array(), array('pcount' => -1), $option->oWhere('pcount > ?', 0));

		$recycleid = $this->_getRecycleAlbumid($uid);
		if ($albumid != $recycleid) {
			$this->_resetAlbumDigest($albumid);
		}
		if ($photo['albumid'] != $recycleid) {
			$this->_resetAlbumDigest($photo['albumid']);
		}
		return true;
	}

	public function changeAlbumTitle($uid, $albumid, $title)
	{
		if (!$uid) {
			return false;
		}
		$recycleid = $this->_getRecycleAlbumid($uid);
		if ($albumid == $recycleid) {
			return false;
		}
		$albumkey = compact('uid', 'albumid');
		$album = $this->albumDao->aGet($albumkey);
		if (empty($album)) {
			return false;
		}
		\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::PHOTO_ALBUM_TITLE.'_'.$albumid, array(
			'update' => $title,
		));
		return true;
	}

	public function changeAlbumDesc($uid, $albumid, $desc)
	{
		if (!$uid) {
			return false;
		}
		$recycleid = $this->_getRecycleAlbumid($uid);
		if ($albumid == $recycleid) {
			return false;
		}
		$albumkey = compact('uid', 'albumid');
		$album = $this->albumDao->aGet($albumkey);
		if (empty($album)) {
			return false;
		}
		\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::PHOTO_ALBUM_DESC.'_'.$albumid, array(
			'update' => $desc,
		));
		return true;
	}

	public function deletePhoto($uid, $photoid)
	{
		if (!$uid) {
			return false;
		}
		$photokey = compact('uid', 'photoid');
		$photo = $this->photoDao->aGet($photokey);
		if (empty($photo)) {
			return false;
		}
		$albumkey = array(
			'uid' => $uid,
			'albumid' => $photo['albumid'],
		);
		$recycleid = $this->_getRecycleAlbumid($uid);
		if ($recycleid == $photo['albumid']) {
			$this->photoDao->iDelete($photokey);
			$option = new \Ko_Tool_SQL();
			$this->albumDao->iUpdate($albumkey, array(), array('pcount' => -1), $option->oWhere('pcount > ?', 0));
		} else {
			$this->photoDao->iUpdate($photokey, array('albumid' => $recycleid, 'sort' => time()));
			$option = new \Ko_Tool_SQL();
			$this->albumDao->iUpdate($albumkey, array(), array('pcount' => -1), $option->oWhere('pcount > ?', 0));
			$recyclekey = array(
				'uid' => $uid,
				'albumid' => $recycleid,
			);
			$this->albumDao->iUpdate($recyclekey, array(), array('pcount' => 1));

			$this->_resetAlbumDigest($photo['albumid']);
		}
		return true;
	}

	public function deleteAlbum($uid, $albumid)
	{
		if (!$uid) {
			return false;
		}
		$recycleid = $this->_getRecycleAlbumid($uid);
		if ($albumid == $recycleid) {
			return false;
		}
		$albumkey = compact('uid', 'albumid');
		$album = $this->albumDao->aGet($albumkey);
		if (empty($album)) {
			return false;
		}
		$option = new \Ko_Tool_SQL();
		$pcount = $this->photoDao->iUpdateByCond($option->oWhere('albumid = ?', $albumid), array('albumid' => $recycleid, 'sort' => time()));
		$this->albumDao->iDelete($albumkey);
		$recyclekey = array(
			'uid' => $uid,
			'albumid' => $recycleid,
		);
		$this->albumDao->iUpdate($recyclekey, array(), array('pcount' => $pcount));
		return true;
	}

	public function addPhoto(&$albumid, $uid, $image, $title = '')
	{
		if (!$uid) {
			return 0;
		}
		if ($albumid) {
			$albumkey = compact('uid', 'albumid');
			$album = $this->albumDao->aGet($albumkey);
			if (empty($album)) {
				$albumid = 0;
			}
		}
		if (!$albumid) {
			$albumtag = date('Y-m');
			$albumid = $this->_albumTag2Id($uid, $albumtag, $albumtag);
		}
		if (!$albumid) {
			return 0;
		}
		$data = compact('albumid', 'uid', 'image');
		$time = time();
		$data['sort'] = $time;
		$data['ctime'] = date('Y-m-d H:i:s', $time);
		$photoid = $this->photoDao->iInsert($data);
		if ($photoid) {
			if (strlen($title)) {
				\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::PHOTO_TITLE.'_'.$photoid, array(
					'update' => $title,
				));
			}

			$albumkey = compact('uid', 'albumid');
			$update = array();
			$time = time();
			$update['sort'] = $time;
			$update['mtime'] = date('Y-m-d H:i:s', $time);
			$this->albumDao->iUpdate($albumkey, $update, array('pcount' => 1));

			$this->_resetAlbumDigest($albumid);
		}
		return $photoid;
	}

	public function addAlbum($uid, $title, $desc)
	{
		if (!$uid) {
			return 0;
		}
		return $this->_addAlbum($uid, $title, $desc);
	}

	private function _getRecycleAlbumid($uid)
	{
		return $this->_albumTag2Id($uid, 'recycle', '回收站');
	}

	private function _albumTag2Id($uid, $albumtag, $albumtitle)
	{
		$albumtagkey = compact('uid', 'albumtag');
		$info = $this->albumtagDao->aGet($albumtagkey);
		if (!empty($info)) {
			$albumid = $info['albumid'];
			$albumkey = compact('uid', 'albumid');
			$albuminfo = $this->albumDao->aGet($albumkey);
			if (!empty($albuminfo)) {
				return $info['albumid'];
			}
			$this->albumtagDao->iDelete($albumtagkey);
		}
		$albumid = $this->_addAlbum($uid, $albumtitle, '');
		if ($albumid) {
			$data = compact('uid', 'albumtag', 'albumid');
			$data['ctime'] = date('Y-m-d H:i:s');
			$this->albumtagDao->aInsert($data);
		}
		return $albumid;
	}

	private function _addAlbum($uid, $title, $desc)
	{
		$time = time();
		$data = array(
			'uid' => $uid,
			'sort' => $time,
			'ctime' => date('Y-m-d H:i:s', $time),
			'mtime' => date('Y-m-d H:i:s', $time),
		);
		$albumid = $this->albumDao->iInsert($data);
		if ($albumid) {
			\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::PHOTO_ALBUM_TITLE.'_'.$albumid, array(
				'update' => $title,
			));
			\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::PHOTO_ALBUM_DESC.'_'.$albumid, array(
				'update' => $desc,
			));
		}
		return $albumid;
	}

	private function _resetAlbumDigest($albumid)
	{
		$option = new \Ko_Tool_SQL();
		$photolist = $this->photoDao->aGetList($option->oSelect('photoid')->oWhere('albumid = ?', $albumid)->oLimit(1000)->oOrderBy('sort desc, photoid desc'));
		$count = count($photolist);
		$step = max(1, floor($count / 50));
		$total = min($count, 50 * $step);
		$photoids = array();
		for ($i = 0; $i < $total; $i += $step) {
			$photoids[] = $photolist[$i]['photoid'];
		}
		$this->_setDigest($albumid, $photoids);
	}

	private function _getDigest($albumids)
	{
		$info = $this->albumdigestDao->aGetListByKeys($albumids);
		foreach ($info as &$v) {
			if (!empty($v)) {
				$v = unserialize($v['photoids']);
			}
		}
		unset($v);
		return $info;
	}

	private function _setDigest($albumid, $photoids)
	{
		$strphotoids = serialize($photoids);
		$data = array(
			'albumid' => $albumid,
			'photoids' => $strphotoids,
		);
		$update = array(
			'photoids' => $strphotoids,
		);
		$this->albumdigestDao->aInsert($data, $update);
	}
}
