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
		$loginuid = \APPS\user\MFacade_Api::getLoginUid();

		$contentApi = new \APPS\content\MFacade_Api();
		if ($loginuid) {
			switch ($put_style)
			{
				case 'default':
					$contentApi->bSet(\APPS\content\MFacade_Const::DRAFT_CONTENT, $loginuid, $update['content']);
					$contentApi->bSet(\APPS\content\MFacade_Const::DRAFT_TITLE, $loginuid, $update['title']);
					break;
				case 'title':
					$contentApi->bSet(\APPS\content\MFacade_Const::DRAFT_TITLE, $loginuid, $update['title']);
					break;
			}
		}
	}
}
