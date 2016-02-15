<?php

namespace APPS\render;

class Mbase extends \Ko_View_Render_Smarty
{
	public function __construct($oSmarty = null)
	{
		parent::__construct($oSmarty);

		$uid = \APPS\user\MFacade_Api::getLoginUid();
		$logininfo = $uid ? \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo32'))) : array();

		$this->oSetData('IMG_DOMAIN', IMG_DOMAIN)
			->oSetData('WWW_DOMAIN', WWW_DOMAIN)
			->oSetData('PASSPORT_DOMAIN', PASSPORT_DOMAIN)
			->oSetData('logininfo', $logininfo);
	}
}
