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
			$contentApi = new \APPS\content\MFacade_Api();
			$contentApi->bSet(\APPS\content\MFacade_Const::USER_NICKNAME, $uid, $nickname);
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
				$image = \APPS\storage\MFacade_Api::weburl2storage($userinfo['logo'], true);
				if ($image !== false)
				{
					$data['logo'] = $image;
				}
			}
			$this->aInsert($data, $data);
			$contentApi = new \APPS\content\MFacade_Api();
			$contentApi->bSet(\APPS\content\MFacade_Const::USER_NICKNAME, $uid, $userinfo['nickname']);
		}
		return true;
	}

}
