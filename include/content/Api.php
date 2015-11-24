<?php

class KContent_Api
{
	public function aGetHtmlEx($aInfo)
	{
		$list = Ko_Apps_Rest::VInvoke('content', 'GET', 'items/', array(
			'filter_style' => 'html',
			'filter' => $aInfo,
		));
		return $list['list'];
	}
}
