<?php

namespace APPS\content;

class MApi extends \Ko_Mode_Content
{
	protected $_aConf = array(
		'contentApi' => 'Func',
		'app' => array(
			\KContent_Const::DRAFT_CONTENT => array(
				'type' => 'html',
			),
			\KContent_Const::DRAFT_TITLE => array(
				'type' => 'text',
			),
			\KContent_Const::BLOG_TITLE => array(
				'type' => 'text',
			),
			\KContent_Const::BLOG_CONTENT => array(
				'type' => 'html',
			),
			\KContent_Const::USER_NICKNAME => array(
				'type' => 'text',
			),
			\KContent_Const::PHOTO_ALBUM_TITLE => array(
				'type' => 'text',
			),
			\KContent_Const::PHOTO_ALBUM_DESC => array(
				'type' => 'text',
			),
			\KContent_Const::PHOTO_TITLE => array(
				'type' => 'text',
			),
		),
	);

	protected function _sDataUrl2Link($sData)
	{
		$data = \Ko_Apps_Rest::VInvoke('storage', 'POST', 'dataurl/', array('update' => $sData), $errno);
		if ($errno)
		{
			return parent::_sDataUrl2Link($sData);
		}
		return $data['key'];
	}
}
