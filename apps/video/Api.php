<?php

namespace APPS\video;

//状态码，0（成功），1（等待处理），2（正在处理），3（处理失败），4（通知提交失败）。
class MApi extends \Ko_Busi_Api
{
	public function add($uid, $video, $persistentid)
	{
		$data = compact('uid', 'video', 'persistentid');
		$data['ctime'] = date('Y-m-d H:i:s');
		$data['p_code'] = 1;    //等待处理
		return $this->listDao->iInsert($data);
	}

	public function updatePinfo($persistentid, $p_code, $p_info)
	{
		$p_info = \Ko_Tool_Enc::SEncode($p_info);
		$update = compact('p_code', 'p_info');
		$option = new \Ko_Tool_SQL();
		return $this->listDao->iUpdateByCond($option->oWhere('persistentid = ?', $persistentid), $update);
	}

	public function getUserList($uid)
	{
		$option = new \Ko_Tool_SQL();
		$list = $this->listDao->aGetList($option->oWhere('uid = ?', $uid)->oOrderBy('videoid desc'));
		foreach ($list as &$v)
		{
			if (0 == $v['p_code'])
			{
				$v['p_info'] = \Ko_Tool_Enc::ADecode($v['p_info']);
				if (false !== $v['p_info'])
				{
					foreach ($v['p_info']['items'] as $pinfo)
					{
						if ('avthumb/mp4' == $pinfo['cmd'])
						{
							$v['videourl'] = \APPS\storage\MFacade_Api::getUrl($pinfo['key'], '');
						}
						else if ('vframe/jpg/offset/0/w/480/h/360' == $pinfo['cmd'])
						{
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
