<?php
class response{
private $code=200,$body;
private $cache=0;
//private $header = array('content-type' => 'application/json');
private $header = array('content-type' => 'text/html');
function response($resobj=null)
{
	$this->set($resobj);
}

function set($resobj=null)
{
	if($resobj){
		if(isset($resobj["code"])){
			$this->code = $resobj["code"];
		}
		
		if(isset($resobj["body"])){
			$this->body = $resobj["body"];
		}
		
		if(isset($resobj["header"])){
			$this->header = $resobj["header"];
		}
		
		if(isset($resobj["cache"])){
			$this->cache = $resobj["cache"];
		}
	}	
}
function flush()
{
	foreach ($this->header as $name => $value) {
            header($name.': '.$value, true, $this->code);
    }
	
	if($this->cache > 0){
		header('Cache-Control:max-age='.$this->cache.', must-revalidate');
	}else{
		header('Cache-Control:no-cache');
	}
	
	if($this->body){
		if($this->code == 200){/**/
			echo json_encode($this->body);
		}else{
			echo $this->body;
		}	
	}
}

}


?>