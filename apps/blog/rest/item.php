<?php

namespace APPS\blog;

class MRest_item
{
	public static $s_aConf = array(
		'unique' => array('hash', array(
			'uid' => 'int',
			'blogid' => 'int',
		)),
		'poststylelist' => array(
			'default' => array('hash', array(
				'title' => 'string',
				'content' => 'string',
				'tags' => 'string',
			)),
		),
		'putstylelist' => array(
			'default' => array('hash', array(
				'title' => 'string',
				'content' => 'string',
				'tags' => 'string',
			)),
			'reset' => 'any',
		),
	);

	public function str2key($str)
	{
		list($uid, $blogid) = explode('_', $str);
		return compact('uid', 'blogid');
	}

	public function post($update, $after = null, $post_style = 'default')
	{
		$loginApi = new \KUser_loginApi();
		$loginuid = $loginApi->iGetLoginUid();

		if (0 == strlen($update['title'])) {
			throw new \Exception('请输入博客标题', 1);
		}

		$blogApi = new MApi();
		$blogid = $blogApi->iInsert($loginuid, $update['title'], $update['content'], $update['tags']);
		if (!$blogid) {
			throw new \Exception('添加博客失败', 2);
		}

		\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_CONTENT.'_'.$loginuid, array(
			'update' => '',
		));
		\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_TITLE.'_'.$loginuid, array(
			'update' => '',
		));

		$this->_sendSysmsg($loginuid, $blogid);

		return array('key' => array('uid' => $loginuid, 'blogid' => $blogid));
	}

	public function put($id, $update, $before = null, $after = null, $put_style = 'default')
	{
		$loginApi = new \KUser_loginApi();
		$loginuid = $loginApi->iGetLoginUid();
		if ($loginuid != $id['uid']) {
			throw new \Exception('修改博客失败', 1);
		}

		$blogApi = new MApi();
		switch ($put_style)
		{
			case 'default':
				$blogApi->iUpdate($loginuid, $id['blogid'], $update['title'], $update['content'], $update['tags']);
				break;
			case 'reset':
				$blogApi->iReset($loginuid, $id['blogid']);
				break;
		}
		return array('key' => $id);
	}

	public function delete($id, $before = null)
	{
		$loginApi = new \KUser_loginApi();
		$loginuid = $loginApi->iGetLoginUid();
		if ($loginuid != $id['uid']) {
			throw new \Exception('删除博客失败', 1);
		}

		$blogApi = new MApi();
		$blogApi->iDelete($loginuid, $id['blogid']);
		return array('key' => $id);
	}

	private function _sendSysmsg($uid, $blogid)
	{
		if (18 <= $uid && $uid <= 21) {
			$content = compact('uid', 'blogid');
			$sysmsgApi = new \KSysmsg_Api();
			$sysmsgApi->iSend(0, \KSysmsg_Api::BLOG, $content, $blogid);
		}
	}
}