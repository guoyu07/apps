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
			MFacade_Const::HOLDEM_ROOM_NAME => array(
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

	public static function FillItemInfo(&$item, $idfield, $typemap, $ishtml = false)
	{
		$list = array($item);
		self::FillListInfo($list, $idfield, $typemap, $ishtml);
		$item = $list[0];
	}

	public static function FillListInfo(&$list, $idfield, $typemap, $ishtml = false)
	{
		$api = new self;
		$ids = \Ko_Tool_Utils::AObjs2ids($list, $idfield);
		$info = array();
		foreach ($typemap as $type => $field)
		{
			if (is_array($field))
			{
				$field['ids'] = $ids;
				$info[$type] = $field;
			}
			else
			{
				$info[$type] = $ids;
			}
		}
		$infos = $ishtml ? $api->aGetHtmlEx($info) : $api->aGetTextEx($info);
		foreach ($list as &$v)
		{
			if (!empty($v))
			{
				foreach ($typemap as $type => $field)
				{
					if (is_array($field))
					{
						$field = $field['field'];
					}
					$v[$field] = $infos[$type][$v[$idfield]];
				}
			}
		}
		unset($v);
	}
}
