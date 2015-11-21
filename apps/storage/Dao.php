<?php

namespace APPS\storage;

class MDao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'urlmap' => array(
			'type' => 'db_single',
			'kind' => 'image_urlmap',
			'key' => 'url',
		),
		'uni' => array(
			'type' => 'db_single',
			'kind' => 'image_uni',
			'key' => 'md5',
		),
		'size' => array(
			'type' => 'db_single',
			'kind' => 'image_size',
			'key' => 'dest',
		),
		'fileinfo' => array(
			'type' => 'db_single',
			'kind' => 'image_fileinfo',
			'key' => 'dest',
		),
		'exif' => array(
			'type' => 'db_single',
			'kind' => 'image_exif',
			'key' => 'dest',
		),
	);
}
