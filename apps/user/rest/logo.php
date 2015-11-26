<?php

namespace APPS\user;

class MRest_logo
{
	public static $s_aConf = array(
		'unique' => 'string',
		'stylelist' => array(
			'default' => 'string',
		),
		'poststylelist' => array(
			'default' => array('hash', array(
				'fileid' => 'string',
				'width' => 'int',
				'height' => 'int',
				'left' => 'int',
				'top' => 'int',
				'w' => 'int',
				'h' => 'int',
			)),
		),
	);

	public function post($update, $after = null)
	{
		$content = \Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$update['fileid'], array('data_style' => 'content'));
		if ('' === $content)
		{
			throw new \Exception('获取原文件失败', 1);
		}
		if (false === ($info = \Ko_Tool_Image::VInfo($content, \Ko_Tool_Image::FLAG_SRC_BLOB)))
		{
			throw new \Exception('获取原文件信息失败', 2);
		}
		switch($info['orientation'])
		{
			case 1:
				$zoom = $info['width'] / $update['width'];
				break;
			case 2:
				$zoom = $info['width'] / $update['width'];
				$content = \Ko_Tool_Image::VFlipH($content, '1.'.$info['type'],
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
			case 3:
				$zoom = $info['width'] / $update['width'];
				$content = \Ko_Tool_Image::VRotate($content, '1.'.$info['type'], 180, 0xffffff,
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
			case 4:
				$zoom = $info['width'] / $update['width'];
				$content = \Ko_Tool_Image::VFlipV($content, '1.'.$info['type'],
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
			case 5:
				$zoom = $info['height'] / $update['width'];
				$content = \Ko_Tool_Image::VRotate($content, '1.'.$info['type'], 90, 0xffffff,
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				$content = \Ko_Tool_Image::VFlipH($content, '1.'.$info['type'],
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
			case 6:
				$zoom = $info['height'] / $update['width'];
				$content = \Ko_Tool_Image::VRotate($content, '1.'.$info['type'], 90, 0xffffff,
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
			case 7:
				$zoom = $info['height'] / $update['width'];
				$content = \Ko_Tool_Image::VRotate($content, '1.'.$info['type'], 90, 0xffffff,
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				$content = \Ko_Tool_Image::VFlipV($content, '1.'.$info['type'],
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
			case 8:
				$zoom = $info['height'] / $update['width'];
				$content = \Ko_Tool_Image::VRotate($content, '1.'.$info['type'], 270, 0xffffff,
					\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB);
				break;
		}
		$aOption = array(
			'srcx' => $zoom * $update['left'],
			'srcy' => $zoom * $update['top'],
			'srcw' => $zoom * $update['w'],
			'srch' => $zoom * $update['h'],
			'quality' => 98,
			'strip' => true,
		);
		if (false === ($dst = \Ko_Tool_Image::VCrop($content, '1.'.$info['type'], $update['w'], $update['h'],
				\Ko_Tool_Image::FLAG_SRC_BLOB | \Ko_Tool_Image::FLAG_DST_BLOB,
				$aOption)))
		{
			throw new \Exception('文件转换失败', 3);
		}
		$data = \Ko_Apps_Rest::VInvoke('storage', 'POST', 'item/', array(
			'post_style' => 'content',
			'update' => array(
				'content' => $dst,
			),
		), $errno);
		if ($errno)
		{
			throw new \Exception('文件保存失败', 3);
		}
		$logoid = $data['key'];

		$loginApi = new MloginApi;
		$baseinfoApi = new MbaseinfoApi;
		$baseinfoApi->bUpdateLogo($loginApi->iGetLoginUid(), $logoid);
		return array('key' => $logoid);
	}
}
