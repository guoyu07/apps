<?php

class KRender_passport extends KRender_base
{
	public function sRender()
	{
		$uid = Ko_Apps_Rest::VInvoke('user', 'GET', 'loginuid/');
		$logininfo = $uid ? Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo32'))) : array();

		$head = new Ko_View_Render_Smarty;
		$head->oSetTemplate('passport/common/header.html')
			->oSetData('IMG_DOMAIN', IMG_DOMAIN)
			->oSetData('WWW_DOMAIN', WWW_DOMAIN)
			->oSetData('PASSPORT_DOMAIN', PASSPORT_DOMAIN)
			->oSetData('logininfo', $logininfo);

		$tail = new Ko_View_Render_Smarty;
		$tail->oSetTemplate('passport/common/footer.html');

		return $head->sRender().parent::sRender().$tail->sRender();
	}
}
