<?php

namespace APPS\storage;

class MApi extends \Ko_Data_Qiniu
{
	protected $_aConf = array(
		'urlmap' => 'urlmap',
		'uni' => 'uni',
		'size' => 'size',
		'fileinfo' => 'fileinfo',
		'exif' => 'exif',
	);

	public function __construct()
	{
		parent::__construct('wI_dH99z7FxEDZcD0t5fkV8Y996_JbeCqIzUlEqF',
			'3YGieZJl6u0Yq7qHruHyusAxs4Mq7R7fdGtg2LIY',
			'kophp',
			'7xawfx.com1.z0.glb.clouddn.com');
	}

	public function aGetAvinfo($sDest)
	{
		$ret = $this->avinfoDao->aGet($sDest);
		$avinfo = \Ko_Tool_Enc::ADecode($ret['avinfo']);
		if (false === $avinfo)
		{
			return array();
		}
		return $avinfo;
	}

	public function vSetAvinfo($sDest, $avinfo)
	{
		$data = array(
			'dest' => $sDest,
			'avinfo' => \Ko_Tool_Enc::SEncode($avinfo),
		);
		$update = array(
			'avinfo' => \Ko_Tool_Enc::SEncode($avinfo),
		);
		$this->avinfoDao->aInsert($data, $update);
	}
}
