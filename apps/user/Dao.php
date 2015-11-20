<?php

class KUser_Oauth2_Dao extends Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'usertoken' => array(
			'type' => 'db_single',
			'kind' => 'oauth2_usertoken',
			'key' => array('uid', 'src', 'token'),
		),
		'lasttoken' => array(
			'type' => 'db_single',
			'kind' => 'oauth2_usertoken_last',
			'key' => array('uid', 'src'),
		),
	);
}
