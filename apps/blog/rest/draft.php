<?php

namespace APPS\blog;

class MRest_draft
{
	public static $s_aConf = array(
		'putstylelist' => array(
			'default' => array(
				'title' => 'string',
				'content' => 'string',
			),
			'title' => array(
				'title' => 'string',
			),
		),
	);

	public function put($id, $update, $before = null, $after = null, $put_style = 'default')
	{
		$loginApi = new \KUser_loginApi();
		$loginuid = $loginApi->iGetLoginUid();

		if ($loginuid) {
			switch ($put_style)
			{
				case 'default':
					\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_CONTENT.'_'.$loginuid, array(
						'update' => $update['content'],
					));
					\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_TITLE.'_'.$loginuid, array(
						'update' => $update['title'],
					));
					break;
				case 'title':
					\Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_TITLE.'_'.$loginuid, array(
						'update' => $update['title'],
					));
					break;
			}
		}
	}
}
