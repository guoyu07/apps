<?php

namespace APPS\content;

class MFacade_Api extends \Ko_Mode_Content
{
	protected $_aConf = array(
		'contentApi' => 'Func',
		'app' => array(
			MFacade_Const::DRAFT_CONTENT => array(
				'type' => 'html',
			),
			MFacade_Const::DRAFT_TITLE => array(
				'type' => 'text',
			),
			MFacade_Const::BLOG_TITLE => array(
				'type' => 'text',
			),
			MFacade_Const::BLOG_CONTENT => array(
				'type' => 'html',
			),
			MFacade_Const::USER_NICKNAME => array(
				'type' => 'text',
			),
			MFacade_Const::PHOTO_ALBUM_TITLE => array(
				'type' => 'text',
			),
			MFacade_Const::PHOTO_ALBUM_DESC => array(
				'type' => 'text',
			),
			MFacade_Const::PHOTO_TITLE => array(
				'type' => 'text',
			),
			MFacade_Const::VIDEO_TITLE => array(
				'type' => 'text',
			),
		),
	);

	protected function _sDataUrl2Link($sData)
	{
		$dest = \APPS\storage\MFacade_Api::content2storage($sData, false);
		if (false === $dest)
		{
			return parent::_sDataUrl2Link($sData);
		}
		return \APPS\storage\MFacade_Api::getUrl($dest, 'imageView2/2/w/600/h/600');
	}
}
