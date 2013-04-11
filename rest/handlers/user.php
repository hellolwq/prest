<?php
require_once(PHPROOT . "/inc/dbase/dbase.php");
class user extends restdb{
public $table_name="users";
public function add($uname,$passwd)
{	
	auth::check_permission(ROLE_ADMIN);
	return parent::add(array('name'=>$uname,'passwd'=>$passwd));
}

public function find_one($uname)
{
	auth::check_permission(ROLE_USER);
	return parent::find_one(array('name'=>$uname));
}

public function test()
{
	return new response(array('body'=>"hello test!!",'cache'=>'5'));
}

public function test2()
{
	return new response(array('body'=>"hello test!!",'cache'=>'aa'));
}
}


?>