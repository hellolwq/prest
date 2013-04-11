<?php
require_once("rest/inc/config.php");
require_once("rest/inc/auth.php");
require_once("rest/inc/response.php");
require_once("rest/inc/restdb.php");
/*url=/rest/{object}/{method}?params...*/
function find_handler($path)
{
	global $g_maps;
	$route_path = substr($path, 5);
	if($g_maps && isset($g_maps[$route_path])){
		$object = $g_maps[$route_path]['class'];
		$action = $g_maps[$route_path]['method'];
	}else if(preg_match('/^\/rest\/(\w+)\/(\w+)\/?[^\/]*$/i',$path,$ret)){
		$object = $ret[1];
		$action = $ret[2];	
	}

	if(isset($object) && isset($action)){
		try {
			$class_file = CLASSES_PATH . "/${object}.php";
			if(file_exists ( $class_file )){
				require_once($class_file);
				$class = new ReflectionClass($object);
				if ($class->isInstantiable()) {
					return array('class'=>$class->newInstance(),'method'=>new ReflectionMethod($object, $action));
				}
			}
		}catch(Exception $e){}
	}
	
	return null;
}
/*extract params from http requeset*/
function extract_params()
{
	return array_merge($_POST,$_GET);
}
/*check sql injection*/
function check_param_safe($input)
{
	if(preg_match("/['=]/",$input)){
		return false;
	}else{
		return true;
	}
}

function check_method_params($method,$params)
{
	$ret = array();
	$func_args = $method->getParameters();
	for($i=0;$i<count($func_args);$i++){
		$arg_name = $func_args[$i]->getName();
		if(isset($params[$arg_name])){
			if(!check_param_safe($params[$arg_name])){
				//echo "check_method_params 1:$arg_name\n";
				return null;
			}
			array_push($ret,$params[$arg_name]);
		}else if($func_args[$i]->isOptional() || $func_args[$i]->isDefaultValueAvailable()){
			continue;
		}
		else{
			//echo "check_method_params 2:$arg_name\n";
			return null;
		}
	}
	return $ret;
}

/*check the ticket*/
$ticket=isset($_COOKIE["ticket"])?$_COOKIE["ticket"]:null;
$resobj= new response();
if($ticket && !auth::check_ticket($ticket)){
	$resobj->set(array('code'=>403,'body'=>"ticket invalid!"));
	goto RES_CLIENT;
}
/*extract a clean and standard path like /rest/xxx/xxx/xxx*/
function filter_path()
{
	$path = preg_replace('/\\|\\\\|\/\//','/',$_SERVER["REQUEST_URI"]);
	$path = preg_replace('/\?[^\/]*$/','',$path);
	$path = preg_replace('/\/$/','',$path);
	return $path;
}
/*find the api handler method*/
$handler = find_handler(filter_path());
if($handler){
	$params = check_method_params($handler['method'],extract_params());
	if(!$params && !is_array($params)){
		$resobj->set(array('code'=>500,'body'=>"params invalid!"));
		goto RES_CLIENT;
	}
	try{
		$resobj = $handler['method']->invokeArgs ($handler['class'],$params);
	}catch(ForbiddenException $e){
		$resobj->set(array('code'=>403,'body'=>$e->getMessage()));
	}catch (Exception $e) {        // Will be caught
		$resobj->set(array('code'=>500,'body'=>$e->getMessage()));
	}
}
else{
	$resobj->set(array('code'=>404));
}

/*response json to client*/
RES_CLIENT:
$resobj->flush();

?>