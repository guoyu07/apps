<?php

namespace APPS\user;

class MbaseinfoApi extends \Ko_Mode_Item
{
	protected $_aConf = array(
		'item' => 'baseinfo',
		'itemlog' => 'changelog',
		'itemlog_kindfield' => 'kind',
		'itemlog_idfield' => 'infoid',
	);

	public function bUpdateNickname($uid, $nickname)
	{
		if ($uid) {
			$data = array(
				'uid' => $uid,
			);
			$this->aInsert($data, $data);
			\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::USER_NICKNAME.'_'.$uid, array(
				'update' => $nickname,
			));
		}
		return true;
	}

	public function bUpdateLogo($uid, $logo)
	{
		if ($uid) {
			$data = array(
				'uid' => $uid,
				'logo' => $logo,
			);
			$this->aInsert($data, $data);
		}
		return true;
	}

	public function bUpdateOauth2info($uid, $userinfo)
	{
		if ($uid) {
			$data = array(
				'uid' => $uid,
			);
			if (strlen($userinfo['logo']))
			{
				$sdata = \Ko_Apps_Rest::VInvoke('storage', 'POST', 'item/', array(
					'post_style' => 'weburl',
					'update' => array(
						'url' => $userinfo['logo'],
					),
				), $errno);
				if (!$errno)
				{
					$data['logo'] = $sdata['key'];
				}
			}
			$this->aInsert($data, $data);
			\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::USER_NICKNAME.'_'.$uid, array(
				'update' => $userinfo['nickname'],
			));
		}
		return true;
	}

}
