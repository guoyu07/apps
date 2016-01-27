<?php

namespace APPS\blog;

class MRest_draft
{
	public static $s_aConf = array(
		'putstylelist' => array(
			'default' => array('hash', array(
				'title' => 'string',
				'content' => 'string',
			)),
			'title' => array('hash', array(
				'title' => 'string',
			)),
		),
	);

	public function put($id, $update, $before = null, $after = null, $put_style = 'default')
	{
		$loginuid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');

		if ($loginuid) {
			switch ($put_style)
			{
				case 'default':
					\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_CONTENT.'_'.$loginuid, array(
						'update' => $update['content'],
					));
					\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_TITLE.'_'.$loginuid, array(
						'update' => $update['title'],
					));
					break;
				case 'title':
					\Ko_App_Rest::VInvoke('content', 'PUT', 'item/'.\KContent_Const::DRAFT_TITLE.'_'.$loginuid, array(
						'update' => $update['title'],
					));
					break;
			}
		}
	}
}
