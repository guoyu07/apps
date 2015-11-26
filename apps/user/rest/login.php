<?php

namespace APPS\user;

class MRest_login
{
	public static $s_aConf = array(
		'unique' => 'int',
		'stylelist' => array(
			'default' => 'int',
		),
		'poststylelist' => array(
			'default' => array('hash', array(
				'username' => 'string',
				'passwd' => 'string',
			)),
		),
	);

	public function post($update, $after = null)
	{
		$api = new MloginApi();
		$uid = $api->iLogin($update['username'], $update['passwd'], $errno);
		if (!$uid) {
			if (\Ko_Mode_User::E_LOGIN_USER == $errno) {
				throw new Exception('用户名不存在', 1);
			}
			if (\Ko_Mode_User::E_LOGIN_PASS == $errno) {
				throw new \Exception('密码错误', 2);
			}
			throw new \Exception('登录失败，请重试', 2);
		}
		$api->vSetLoginUid($uid, 'login');
		return array('key' => $uid);
	}

	public function delete($id, $before = null)
	{
		$api = new MloginApi();
		$api->vSetLoginUid(0);
		return array('key' => $id);
	}
}
