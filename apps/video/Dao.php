<?php

namespace APPS\video;

class MDao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'list' => array(
			'type' => 'db_single',
			'kind' => 'video_list',
			'key' => array('videoid', 'uid'),
		),
	);
}
