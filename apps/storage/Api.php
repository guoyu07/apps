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

	public static function AAdapter($datalist)
	{
		$api = new self;
		$newdatalist = array();
		$dests_withsize = array();
		foreach ($datalist as $v) {
			if (strlen($v[0]) && isset($v[1]['withsize']) && $v[1]['withsize']) {
				$dests_withsize[] = $v[0];
			}
		}
		$sizes = $api->aGetImagesSize($dests_withsize);
		foreach ($datalist as $k => $v) {
			$newdatalist[$k] = array();
			if (strlen($v[0])) {
				if (isset($sizes[$v[0]])) {
					$newdatalist[$k]['size'] = $sizes[$v[0]];
				}
				if (isset($v[1]['brief'])) {
					$newdatalist[$k]['brief'] = $api->sGetUrl($v[0], $v[1]['brief']);
				} else if (isset($v[1]['briefCallback'])) {
					$brief = call_user_func($v[1]['briefCallback'], $newdatalist[$k]);
					$newdatalist[$k]['brief'] = $api->sGetUrl($v[0], $brief);
				}
			}
		}
		return $newdatalist;
	}
}
