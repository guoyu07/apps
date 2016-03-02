<?php

namespace APPS\video;

//状态码，0（成功），1（等待处理），2（正在处理），3（处理失败），4（通知提交失败）。
class MApi extends \Ko_Busi_Api
{
	public function getInfos($list)
	{
		$infos = $this->listDao->aGetDetails($list);
		\APPS\content\MFacade_Api::FillListInfo($infos, 'videoid', array(
			\APPS\content\MFacade_Const::VIDEO_TITLE => 'title',
		));
		$videos = \Ko_Tool_Utils::AObjs2ids($infos, 'video');
		$avinfos = \APPS\storage\MFacade_Api::getAvinfos($videos);
		foreach ($infos as &$v) {
			if ($v['delflag']) {
				$v = array();
			}
			if (!empty($v)) {
				$v['avinfo'] = $avinfos[$v['video']]['avinfo'];
				if (0 == $v['p_code']) {
					$v['p_info'] = \Ko_Tool_Enc::ADecode($v['p_info']);
					if (false !== $v['p_info']) {
						foreach ($v['p_info']['items'] as $pinfo) {
							if ('avthumb/mp4' == $pinfo['cmd']) {
								$v['videourl'] = \APPS\storage\MFacade_Api::getUrl($pinfo['key'], '');
							} else if ('vframe/jpg/offset/0/w/480/h/360' == $pinfo['cmd']) {
								$v['videoimg'] = \APPS\storage\MFacade_Api::getUrl($pinfo['key'], '');
							}
						}
					}
				}
			}
		}
		unset($v);
		return $infos;
	}

	public function add($uid, $video, $persistentid, $title)
	{
		$data = compact('uid', 'video', 'persistentid');
		$data['ctime'] = date('Y-m-d H:i:s');
		$data['p_code'] = 1;    //等待处理
		$videoid = $this->listDao->iInsert($data);
		if ($videoid) {
			$contentApi = new \APPS\content\MFacade_Api();
			$contentApi->bSet(\APPS\content\MFacade_Const::VIDEO_TITLE, $videoid, $title);
			if (18 <= $uid && $uid <= 21) {
				$content = compact('uid', 'videoid');
				\APPS\sysmsg\MFacade_Api::send(0, \APPS\sysmsg\MFacade_Const::VIDEO, $content, $videoid);
			}
		}
		return $videoid;
	}

	public function del($uid, $videoid)
	{
		$key = compact('uid', 'videoid');
		$info = $this->listDao->aGet($key);
		if (!empty($info)) {
			if ($info['delflag']) {
				$this->listDao->iDelete($key);
			} else {
				$this->listDao->iUpdate($key, array('delflag' => 1));
			}
		}
		return true;
	}

	public function recover($uid, $videoid)
	{
		$key = compact('uid', 'videoid');
		$info = $this->listDao->aGet($key);
		if (!empty($info)) {
			$this->listDao->iUpdate($key, array('delflag' => 0));
		}
		return true;
	}

	public function updatePinfo($persistentid, $p_code, $p_info)
	{
		$p_info = \Ko_Tool_Enc::SEncode($p_info);
		$update = compact('p_code', 'p_info');
		$option = new \Ko_Tool_SQL();
		return $this->listDao->iUpdateByCond($option->oWhere('persistentid = ?', $persistentid), $update);
	}

	public function changeTitle($uid, $videoid, $title)
	{
		$key = compact('uid', 'videoid');
		$info = $this->listDao->aGet($key);
		if (!empty($info)) {
			$contentApi = new \APPS\content\MFacade_Api();
			$contentApi->bSet(\APPS\content\MFacade_Const::VIDEO_TITLE, $videoid, $title);
		}
	}

	public function getUserList($uid, $recycle)
	{
		$option = new \Ko_Tool_SQL();
		$list = $this->listDao->aGetList($option->oWhere('uid = ? and delflag = ?', $uid, $recycle)->oOrderBy('videoid desc'));
		\APPS\content\MFacade_Api::FillListInfo($list, 'videoid', array(
			\APPS\content\MFacade_Const::VIDEO_TITLE => 'title',
		));
		$videos = \Ko_Tool_Utils::AObjs2ids($list, 'video');
		$avinfos = \APPS\storage\MFacade_Api::getAvinfos($videos);
		foreach ($list as &$v) {
			$v['avinfo'] = $avinfos[$v['video']]['avinfo'];
			if (0 == $v['p_code']) {
				$v['p_info'] = \Ko_Tool_Enc::ADecode($v['p_info']);
				if (false !== $v['p_info']) {
					foreach ($v['p_info']['items'] as $pinfo) {
						if ('avthumb/mp4' == $pinfo['cmd']) {
							$v['videourl'] = \APPS\storage\MFacade_Api::getUrl($pinfo['key'], '');
						} else if ('vframe/jpg/offset/0/w/480/h/360' == $pinfo['cmd']) {
							$v['videoimg'] = \APPS\storage\MFacade_Api::getUrl($pinfo['key'], '');
						}
					}
				}
			}
		}
		unset($v);
		return $list;
	}
}
