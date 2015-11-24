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
		$nicknames = Ko_Apps_Rest::VInvoke('content', 'GET', 'item/', array(
			'filter' => array(
				'aid' => KContent_Const::USER_NICKNAME,
				'ids' => $uids,
			),
		));
		$nicknames = $nicknames['list'];
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

	public function bUpdateNickname($uid, $nickname)
	{
		if ($uid) {
			$data = array(
				'uid' => $uid,
			);
			$this->aInsert($data, $data);
			Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::USER_NICKNAME.'_'.$uid, array(
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
				$sdata = Ko_Apps_Rest::VInvoke('storage', 'POST', 'item/', array(
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
			Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::USER_NICKNAME.'_'.$uid, array(
				'update' => $userinfo['nickname'],
			));
		}
		return true;
	}
	
	private static function _VFillMoreInfo(&$info, $aMore)
	{
		foreach ($aMore as $more)
		{
			switch($more)
			{
			case 'logo16':
				$info['logo16'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/16.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/16'));
				break;
			case 'logo32':
				$info['logo32'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/32.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/32'));
				break;
			case 'logo48':
				$info['logo48'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/48.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/48'));
				break;
			case 'logo80':
				$info['logo80'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/80.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/80'));
				break;
			case 'logo120':
				$info['logo120'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/120.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/120'));
				break;
			case 'logo200':
				$info['logo200'] = ('' === $info['logo'])
					? 'http://'.IMG_DOMAIN.'/logo/200.png'
					: Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$info['logo'], array('data_decorate' => 'imageView2/1/w/200'));
				break;
			}
		}
	}
}
