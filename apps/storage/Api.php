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

	public function aGetAvinfos($aDest)
	{
		$list = $this->avinfoDao->aGetListByKeys($aDest);
		foreach ($list as &$v) {
			$v['avinfo'] = \Ko_Tool_Enc::ADecode($v['avinfo']);
		}
		unset($v);
		return $list;
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
