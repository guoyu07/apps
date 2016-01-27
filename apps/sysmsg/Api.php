<?php

namespace APPS\sysmsg;

class MApi extends \Ko_Mode_Sysmsg
{
	protected $_aConf = array(
		'content' => 'content',
		'user' => 'user',
		'merge' => 'merge',
		'kind' => array(
			'index' => array(\KSysmsg_Const::PHOTO, \KSysmsg_Const::BLOG),
		),
	);

	public function getIndexList($boundary, $num, &$next, &$next_boundary)
	{
		$msglist = $this->aGetListSeq(0, 'index', $boundary, $num, $next, $next_boundary);

		$userlist = $albumlist = $photolist = $bloglist = array();
		foreach ($msglist as $v) {
			if (\KSysmsg_Const::PHOTO == $v['msgtype']) {
				$userlist[$v['content']['uid']] = $v['content']['uid'];
				$albumlist[] = array('uid' => $v['content']['uid'], 'albumid' => $v['content']['albumid']);
				$photolist = array_merge($photolist, $v['content']['photolist']);
			} else if (\KSysmsg_Const::BLOG == $v['msgtype']) {
				$userlist[$v['content']['uid']] = $v['content']['uid'];
				$bloglist[] = array('uid' => $v['content']['uid'], 'blogid' => $v['content']['blogid']);
			}
		}

		$userlist = \Ko_Tool_Adapter::VConv($userlist, array('list', array('user_baseinfo', array('logo80'))));
		$photoinfos = \Ko_App_Rest::VInvoke('photo', 'GET', 'item/', array(
			'filter_style' => 'photolist',
			'filter' => $photolist,
		));
		$photoinfos = $photoinfos['list'];
		$albuminfos = \Ko_App_Rest::VInvoke('photo', 'GET', 'album/', array(
			'filter' => $albumlist,
		));
		$albuminfos = $albuminfos['list'];
		$bloginfos = \Ko_App_Rest::VInvoke('blog', 'GET', 'item/', array(
			'filter' => $bloglist,
		));
		$bloginfos = $bloginfos['list'];
		foreach ($msglist as $k => &$v) {
			if (\KSysmsg_Const::PHOTO == $v['msgtype']) {
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
							$photo['image'] = \Ko_App_Rest::VInvoke('storage', 'GET',
								'item/'.$photoinfos[$photo['photoid']]['image'], array('data_decorate' => 'imageView2/2/w/480/h/240'));
							$photolist[] = $photo;
						}
					}
					$v['content']['photolist'] = $photolist;
					if (empty($photolist)) {
						$this->vDelete(0, $v['msgid']);
						unset($msglist[$k]);
					}
				}
			} else if (\KSysmsg_Const::BLOG == $v['msgtype']) {
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
