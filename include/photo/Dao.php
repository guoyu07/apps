<?php

class KPhoto_Dao extends Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'albumtag' => array(
			'type' => 'db_single',
			'kind' => 'photo_albumtag',
			'key' => array('uid', 'albumtag'),
		),
		'album' => array(
			'type' => 'db_single',
			'kind' => 'photo_album',
			'key' => array('uid', 'albumid'),
		),
		'photo' => array(
			'type' => 'db_single',
			'kind' => 'photo_list',
			'key' => array('albumid', 'photoid'),
		),
	);
}