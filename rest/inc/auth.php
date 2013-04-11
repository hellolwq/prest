<?php
define('ROLE_ADMIN',1); 
define('ROLE_USER',2); 
define('ROLE_ANONYMOUS',3); 
class ForbiddenException extends Exception
{
 protected $code = 403;
 protected $message = 'The request is denied!';
}

class NotFoundException extends Exception
{
 protected $code = 404;
 protected $message = 'Handler not found!';
}

class auth{
public static function gen_ticket($uname,$role,$seed)
{
	global $g_config;
	$timestamp=time();
	$plain="$uname:$role:$timestamp:";
	return $plain . md5(base64_encode($plain . $seed . ":" . $g_config["md5_salt"]));
}

public static function check_ticket($ticket)
{
	global $g_config;
	if(!preg_match("/^([^:]+:){3}[^:]+$/",$ticket)){
		return false;
	}
	list($uname,$role,$timestamp,$md5str) = explode(':',$ticket);
	$encryp = md5(base64_encode("$uname:$role:$timestamp:" . auth::get_seed() . ":" . $g_config["md5_salt"]));
	if($encryp === $md5str){
		return true;
	}
	return false;
}

public static function get_seed()/*prevent ticket stolen*/
{
	return $_SERVER["HTTP_USER_AGENT"];
}

public static function check_permission($required)
{
	global $ticket;
	if($ticket && auth::check_ticket($ticket,auth::get_seed())){
		list($uname,$role,$timestamp,$md5str) = explode(':',$ticket);
	}else{
		$role = ROLE_ANONYMOUS;
	}

	if($role > $required){/*1为最大角色，其它权限依数字越大，权限越小*/
		throw new ForbiddenException('permission denied');
	}
}

}
?>