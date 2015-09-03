<?php

class KUser_baseinfoApi extends Ko_Mode_Item
{
	protected $_aConf = array(
		'item' => 'baseinfo',
		'itemlog' => 'changelog',
		'itemlog_kindfield' => 'kind',
		'itemlog_idfield' => 'infoid',
	);
	
	public static function AAdapter($datalist)
	{
		$newdatalist = array();
		$uids = array();
		foreach ($datalist as $v)
		{
			$uids[] = $v[0];
		}
		$infos = Ko_Tool_Singleton::OInstance('KUser_baseinfoApi')->aGetListByKeys($uids);
		$contentApi = new KContent_Api;
		$nicknames = $contentApi->aGetText(KContent_Api::USER_NICKNAME, $uids);
		foreach ($datalist as $k => $v)
		{
			$newdatalist[$k] = isset($infos[$v[0]]) ? $infos[$v[0]] : array();
			if (!empty($newdatalist[$k]))
			{
				$newdatalist[$k]['nickname'] = $nicknames[$v[0]];
				self::_VFillMoreInfo($newdatalist[$k], $v[1]);
			}
		}
		return $newdatalist;
	}

	public function aGetAllUser($aMore)
	{
		$option = new Ko_Tool_SQL();
		$infos = $this->aGetList($option->oWhere('1')->oOrderBy('uid desc'));
		$uids = Ko_Tool_Utils::AObjs2ids($infos, 'uid');
		$contentApi = new KContent_Api;
		$nicknames = $contentApi->aGetText(KContent_Api::USER_NICKNAME, $uids);
		foreach ($infos as &$v)
		{
			$v['nickname'] = $nicknames[$v['uid']];
			self::_VFillMoreInfo($v, $aMore);
		}
		unset($v);
		return $infos;
	}

	public function bUpdateNickname($uid, $nickname)
	{
		if ($uid) {
			$data = array(
				'uid' => $uid,
			);
			$this->aInsert($data, $data);
			$contentApi = new KContent_Api;
			$contentApi->bSet(KContent_Api::USER_NICKNAME, $uid, $nickname);
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
				$api = new KStorage_Api;
				if ($api->bWebUrl2Storage($userinfo['logo'], $logo))
				{
					$data['logo'] = $logo;
				}
			}
			$this->aInsert($data, $data);
			$contentApi = new KContent_Api;
			$contentApi->bSet(KContent_Api::USER_NICKNAME, $uid, $userinfo['nickname']);
		}
		return true;
	}
	
	private static function _VFillMoreInfo(&$info, $aMore)
	{
		$api = new KStorage_Api;
		foreach ($aMore as $more)
		{
			switch($more)
			{
			case 'logo16':
				$info['logo16'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/16.png' : $api->sGetUrl($info['logo'], 'imageView2/1/w/16');
				break;
			case 'logo32':
				$info['logo32'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/32.png' : $api->sGetUrl($info['logo'], 'imageView2/1/w/32');
				break;
			case 'logo48':
				$info['logo48'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/48.png' : $api->sGetUrl($info['logo'], 'imageView2/1/w/48');
				break;
			}
		}
	}
}
