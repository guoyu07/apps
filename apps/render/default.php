<?php

namespace APPS\render;

class Mdefault extends Mbase
{
	public function sRender()
	{
		$smarty = new \Ko_View_Smarty();
		$smarty->vSetTemplateDir(KO_APPS_DIR.'render/templates/');

		$head = new Mbase($smarty);
		$head->oSetTemplate('default/header.html');

		$tail = new Mbase($smarty);
		$tail->oSetTemplate('default/footer.html');

		return $head->sRender().parent::sRender().$tail->sRender();
	}
}
