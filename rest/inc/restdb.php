<?php
require_once(PHPROOT . "/inc/dbase/dbase.php");
class restdb{
public $table_name;
public $table_name_full;
function restdb()
{
	global $g_config;
	if($g_config['pre']){
		$this->table_name_full = $g_config['pre'].$this->table_name;
	}
}
public function all()
{
	$db = new Database();
	$db->connect();
	$obj = $db->fetch_all_array("select * from ". $this->table_name_full);
	$db->close();
	return new response(array('body'=>$obj));
}

public function add($obj)
{
	$db = new Database();
	$db->connect();
	$obj = $db->query_insert($this->table_name,$obj);
	$db->close();
	return new response(array('body'=>$obj));
}

public function delete($id)
{
	$db = new Database();
	$db->connect();
	$obj = $db->query("delete from ". $this->table_name_full . " where id=${id}");
	$db->close();
	return new response(array('body'=>$obj));
}

public function find_one($conditions)
{
	$where = "where 1=1";
	foreach($conditions as $key => $value){
			$where .= " and $key=" . (is_numeric($value) ? $value : "'$value' ") ;
	}
	
	$db = new Database();
	$db->connect();
	$obj = $db->query_first("select * from ". $this->table_name_full . " $where");
	$db->close();
	return new response(array('body'=>$obj));
}

public function find($conditions)
{
	$where = "where 1=1";
	foreach($conditions as $key => $value){
			$where .= " and $key=" . (is_numeric($value) ? $value : "'$value' ") ;
	}
	
	$db = new Database();
	$db->connect();
	$obj = $db->query("select * from ". $this->table_name_full . " $where");
	$db->close();
	return new response(array('body'=>$obj));
}

}


?>