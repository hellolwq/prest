<?php
define('PHPROOT',$_SERVER['DOCUMENT_ROOT']."/rest");
define('CLASSES_PATH', PHPROOT.'/'.'handlers');
$g_config = array(
'server'=>'localhost',
'user'=>'root',
'pass'=>'root',
'database'=>'mproject',
'pre'=>'mp_',
'md5_salt'=>'tstec'
);

/*path route table*/
$g_maps = array(
'/ttt'=>array(
	'class'=>'user',
	'method'=>'test'
),
'/abc/test'=>array(
	'class'=>'user',
	'method'=>'test'
)
)

?>