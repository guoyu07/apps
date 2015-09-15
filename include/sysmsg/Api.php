<?php

class KSysmsg_Api extends Ko_Mode_Sysmsg
{
	const PHOTO = 1;
	const BLOG = 2;

	protected $_aConf = array(
		'content' => 'content',
		'user' => 'user',
		'merge' => 'merge',
		'kind' => array(
			'index' => array(self::PHOTO, self::BLOG),
		),
	);

	public function getIndexList($boundary, $num, &$next, &$next_boundary)
	{
		$msglist = $this->aGetListSeq(0, 'index', $boundary, $num, $next, $next_boundary);

		$userlist = $albumlist = $photolist = $bloglist = array();
		foreach ($msglist as $v) {
			if (self::PHOTO == $v['msgtype']) {
				$userlist[$v['content']['uid']] = $v['content']['uid'];
				$albumlist[] = array('uid' => $v['content']['uid'], 'albumid' => $v['content']['albumid']);
				$photolist = array_merge($photolist, $v['content']['photolist']);
			} else if (self::BLOG == $v['msgtype']) {
				$userlist[$v['content']['uid']] = $v['content']['uid'];
				$bloglist[] = array('uid' => $v['content']['uid'], 'blogid' => $v['content']['blogid']);
			}
		}

		$userlist = Ko_Tool_Adapter::VConv($userlist, array('list', array('user_baseinfo', array('logo80'))));
		$storageApi = new KStorage_Api();
		$photoApi = new KPhoto_Api();
		$blogApi = new KBlog_Api();
		$photoinfos = $photoApi->getPhotoInfos($photolist);
		$albuminfos = $photoApi->getAlbumInfos($albumlist);
		$bloginfos = $blogApi->aGetBlogInfos($bloglist);
		foreach ($msglist as $k => &$v) {
			if (self::PHOTO == $v['msgtype']) {
				$v['content']['userinfo'] = $userlist[$v['content']['uid']];
				$v['content']['albuminfo'] = $albuminfos[$v['content']['albumid']];
				if (empty($v['content']['albuminfo'])) {
					$this->vDelete(0, $v['msgid']);
					unset($msglist[$k]);
				} else {
					$photolist = array();
					foreach ($v['content']['photolist'] as $photo) {
						if (!empty($photoinfos[$photo['photoid']])
							&& $photoinfos[$photo['photoid']]['albumid'] == $photo['albumid']) {
							$photo['image'] = $storageApi->sGetUrl($photoinfos[$photo['photoid']]['image'], 'imageView2/2/w/480/h/240');
							$photolist[] = $photo;
						}
					}
					$v['content']['photolist'] = $photolist;
					if (empty($photolist)) {
						$this->vDelete(0, $v['msgid']);
						unset($msglist[$k]);
					}
				}
			} else if (self::BLOG == $v['msgtype']) {
				$v['content']['userinfo'] = $userlist[$v['content']['uid']];
				$v['content']['bloginfo'] = $bloginfos[$v['content']['blogid']];
				if (empty($v['content']['bloginfo'])) {
					$this->vDelete(0, $v['msgid']);
					unset($msglist[$k]);
				}
			}
		}
		unset($v);

		return $msglist;
	}
}
